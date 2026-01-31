package app.homelandabason.com;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.os.Build;
import android.telephony.TelephonyManager;
import android.util.Log;
import androidx.core.content.ContextCompat;
import com.getcapacitor.Bridge;
import com.getcapacitor.PluginCall;

public class CallReceiver extends BroadcastReceiver {
    private static final String TAG = "CallReceiver";
    private static Bridge bridge;
    private static String lastPhoneNumber = null;
    private static String lastCallState = null;

    public static void setBridge(Bridge bridgeInstance) {
        bridge = bridgeInstance;
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        // Check permissions first
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            boolean hasPermission = ContextCompat.checkSelfPermission(context, android.Manifest.permission.READ_PHONE_STATE) == PackageManager.PERMISSION_GRANTED;
            if (!hasPermission) {
                Log.e(TAG, "=== PERMISSION DENIED: READ_PHONE_STATE ===");
                Log.e(TAG, "CallReceiver cannot work without READ_PHONE_STATE permission!");
                return;
            }
        }
        String state = intent.getStringExtra(TelephonyManager.EXTRA_STATE);
        String phoneNumber = intent.getStringExtra(TelephonyManager.EXTRA_INCOMING_NUMBER);

        Log.d(TAG, "=== CallReceiver triggered ===");
        Log.d(TAG, "Call state: " + state);
        Log.d(TAG, "Phone number: " + phoneNumber);
        Log.d(TAG, "Last call state: " + lastCallState);
        Log.d(TAG, "Last phone number: " + lastPhoneNumber);

        if (state != null && !state.equals(lastCallState)) {
            lastCallState = state;

            if (TelephonyManager.EXTRA_STATE_RINGING.equals(state)) {
                // Incoming call detected
                Log.d(TAG, "Incoming call RINGING state detected");
                
                // On Android 10+, phone number might be null due to privacy restrictions
                // Still open the app even if phone number is null
                if (phoneNumber != null && !phoneNumber.equals(lastPhoneNumber)) {
                    lastPhoneNumber = phoneNumber;
                    Log.d(TAG, "Incoming call from: " + phoneNumber);
                    
                    // Open app and navigate to customer create page
                    openAppWithPhoneNumber(context, phoneNumber);
                } else if (phoneNumber == null) {
                    Log.w(TAG, "Phone number is null (may be due to Android 10+ privacy restrictions)");
                    // Still open app but without phone number
                    openAppWithPhoneNumber(context, "");
                } else {
                    Log.d(TAG, "Same phone number, skipping");
                }
            } else if (TelephonyManager.EXTRA_STATE_IDLE.equals(state)) {
                // Call ended
                Log.d(TAG, "Call ended (IDLE state)");
                lastPhoneNumber = null;
                stopForegroundHelper(context);
            }
        } else {
            Log.d(TAG, "State unchanged or null, ignoring");
        }
    }

    private void openAppWithPhoneNumber(Context context, String phoneNumber) {
        try {
            Log.d(TAG, "=== FORCEFULLY OPENING APP ===");
            Log.d(TAG, "Phone number: " + (phoneNumber != null ? phoneNumber : "null"));
            Log.d(TAG, "Context: " + context.getClass().getName());
            
            // Create intent to open MainActivity with ALL flags to force open
            Intent appIntent = new Intent(context, MainActivity.class);
            
            // Critical flags to force open app even from sleep/background
            appIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            appIntent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            appIntent.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP);
            appIntent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
            appIntent.addFlags(Intent.FLAG_ACTIVITY_BROUGHT_TO_FRONT);
            appIntent.addFlags(Intent.FLAG_ACTIVITY_RESET_TASK_IF_NEEDED);
            appIntent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TASK);
            appIntent.addFlags(Intent.FLAG_ACTIVITY_EXCLUDE_FROM_RECENTS);
            
            // Add phone number and call flag
            if (phoneNumber != null && !phoneNumber.isEmpty()) {
                appIntent.putExtra("phone_number", phoneNumber);
            }
            appIntent.putExtra("from_call", true);
            appIntent.putExtra("force_open", true);
            
            Log.d(TAG, "Intent created with all flags, starting activity...");
            
            // Start a foreground service that shows a full-screen notification (more reliable on Android 10+)
            startForegroundHelper(context, phoneNumber);

            // Start activity - this will FORCEFULLY wake up the app even from sleep mode
            context.startActivity(appIntent);
            
            Log.d(TAG, "=== APP FORCEFULLY OPENED ===");
        } catch (Exception e) {
            Log.e(TAG, "=== ERROR opening app ===");
            Log.e(TAG, "Error message: " + e.getMessage());
            Log.e(TAG, "Error class: " + e.getClass().getName());
            e.printStackTrace();
        }
    }

    private void startForegroundHelper(Context context, String phoneNumber) {
        try {
            Intent svc = new Intent(context, CallForegroundService.class);
            svc.setAction(CallForegroundService.ACTION_START);
            if (phoneNumber != null) {
                svc.putExtra("phone_number", phoneNumber);
            }

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                ContextCompat.startForegroundService(context, svc);
            } else {
                context.startService(svc);
            }
        } catch (Exception e) {
            Log.w(TAG, "Failed to start foreground service: " + e.getMessage());
        }
    }

    private void stopForegroundHelper(Context context) {
        try {
            Intent svc = new Intent(context, CallForegroundService.class);
            svc.setAction(CallForegroundService.ACTION_STOP);
            context.startService(svc);
        } catch (Exception e) {
            Log.w(TAG, "Failed to stop foreground service: " + e.getMessage());
        }
    }
}

