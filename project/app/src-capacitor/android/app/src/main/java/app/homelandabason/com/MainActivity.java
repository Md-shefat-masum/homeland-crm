package app.homelandabason.com;

import android.Manifest;
import android.content.ContentResolver;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.database.Cursor;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.PowerManager;
import android.provider.CallLog;
import android.provider.Settings;
import android.util.Log;
import android.view.WindowManager;
import android.webkit.JavascriptInterface;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import com.getcapacitor.BridgeActivity;
import app.homelandabason.com.calllog.CallLogPlugin;
import app.homelandabason.com.CallDetectionPlugin;
import java.util.ArrayList;

public class MainActivity extends BridgeActivity {
    private static final String TAG = "MainActivity";
    private static final int PERMISSION_REQUEST_CODE = 1001;
    private static final int OVERLAY_PERMISSION_REQUEST_CODE = 1002;
    private static String pendingPhoneNumber = null;
    private PowerManager.WakeLock wakeLock;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        // Register Capacitor plugins BEFORE super.onCreate() as per official documentation
        registerPlugin(CallLogPlugin.class);
        registerPlugin(CallDetectionPlugin.class);
        
        super.onCreate(savedInstanceState);
        
        setupWindowFlags();
        requestPhonePermissions();
        checkOverlayPermission();
        
        CallReceiver.setBridge(this.getBridge());
        handleIntent(getIntent());
        
        if (getBridge() != null && getBridge().getWebView() != null) {
            getBridge().getWebView().addJavascriptInterface(new WebAppInterface(), "Android");
        }
    }

    private void setupWindowFlags() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O_MR1) {
            setShowWhenLocked(true);
            setTurnScreenOn(true);
            getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON |
                                WindowManager.LayoutParams.FLAG_ALLOW_LOCK_WHILE_SCREEN_ON);
        } else {
            getWindow().addFlags(WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED |
                                WindowManager.LayoutParams.FLAG_DISMISS_KEYGUARD |
                                WindowManager.LayoutParams.FLAG_TURN_SCREEN_ON |
                                WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
        }
    }

    private void checkOverlayPermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (!Settings.canDrawOverlays(this)) {
                Intent intent = new Intent(Settings.ACTION_MANAGE_OVERLAY_PERMISSION,
                        Uri.parse("package:" + getPackageName()));
                startActivityForResult(intent, OVERLAY_PERMISSION_REQUEST_CODE);
            }
        }
    }

    private void requestPhonePermissions() {
        ArrayList<String> toRequest = new ArrayList<>();
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_PHONE_STATE) != PackageManager.PERMISSION_GRANTED) {
            toRequest.add(Manifest.permission.READ_PHONE_STATE);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_CALL_LOG) != PackageManager.PERMISSION_GRANTED) {
            toRequest.add(Manifest.permission.READ_CALL_LOG);
        }
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                toRequest.add(Manifest.permission.POST_NOTIFICATIONS);
            }
        }

        if (!toRequest.isEmpty()) {
            ActivityCompat.requestPermissions(this, toRequest.toArray(new String[0]), PERMISSION_REQUEST_CODE);
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        Intent intent = getIntent();
        if (intent != null && (intent.getBooleanExtra("from_call", false) || intent.getBooleanExtra("force_open", false))) {
            wakeUpScreen();
        }
    }

    private void wakeUpScreen() {
        try {
            PowerManager powerManager = (PowerManager) getSystemService(POWER_SERVICE);
            if (powerManager != null) {
                if (wakeLock != null && wakeLock.isHeld()) wakeLock.release();
                wakeLock = powerManager.newWakeLock(
                    PowerManager.SCREEN_BRIGHT_WAKE_LOCK | 
                    PowerManager.ACQUIRE_CAUSES_WAKEUP |
                    PowerManager.FULL_WAKE_LOCK,
                    "HomelandCRM::CallWakeLock"
                );
                wakeLock.acquire(5 * 60 * 1000L); // 5 minutes
            }
        } catch (Exception e) {
            Log.e(TAG, "Error waking up screen: " + e.getMessage());
        }
    }

    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        setIntent(intent);
        handleIntent(intent);
    }

    private void handleIntent(Intent intent) {
        if (intent != null && intent.getBooleanExtra("from_call", false)) {
            String phoneNumber = intent.getStringExtra("phone_number");
            if (phoneNumber == null || phoneNumber.isEmpty()) {
                phoneNumber = getLastIncomingCallNumber();
            }
            final String finalPhoneNumber = (phoneNumber != null) ? phoneNumber : "";
            pendingPhoneNumber = finalPhoneNumber;
            
            if (getBridge() != null && getBridge().getWebView() != null) {
                getBridge().getWebView().postDelayed(() -> {
                    if (getBridge() != null && getBridge().getWebView() != null) {
                        String escapedPhone = finalPhoneNumber.replace("'", "\\'").replace("\n", "").replace("\r", "");
                        // Navigating to route in Quasar
                        String route = "/crm/leads/create?phone_number=" + escapedPhone;
                        String js = "window.location.hash = '#" + route + "';"; 
                        getBridge().getWebView().evaluateJavascript(js, null);
                    }
                }, 1500);
            }
        }
    }

    private String getLastIncomingCallNumber() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_CALL_LOG) != PackageManager.PERMISSION_GRANTED) {
            return null;
        }
        try {
            ContentResolver resolver = getContentResolver();
            String[] projection = {CallLog.Calls.NUMBER, CallLog.Calls.TYPE, CallLog.Calls.DATE};
            String selection = CallLog.Calls.TYPE + " = ?";
            String[] selectionArgs = {String.valueOf(CallLog.Calls.INCOMING_TYPE)};
            String sortOrder = CallLog.Calls.DATE + " DESC LIMIT 1";

            Cursor cursor = resolver.query(CallLog.Calls.CONTENT_URI, projection, selection, selectionArgs, sortOrder);
            if (cursor != null && cursor.moveToFirst()) {
                int numberIndex = cursor.getColumnIndex(CallLog.Calls.NUMBER);
                if (numberIndex >= 0) {
                    String number = cursor.getString(numberIndex);
                    cursor.close();
                    return number;
                }
                cursor.close();
            }
        } catch (Exception e) {
            Log.e(TAG, "Error: " + e.getMessage());
        }
        return null;
    }

    public class WebAppInterface {
        @JavascriptInterface
        public String getPendingPhoneNumber() {
            String phone = pendingPhoneNumber;
            pendingPhoneNumber = null;
            return phone != null ? phone : "";
        }
    }
}
