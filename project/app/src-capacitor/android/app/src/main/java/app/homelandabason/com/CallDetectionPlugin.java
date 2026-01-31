package app.homelandabason.com;

import android.Manifest;
import android.content.ContentResolver;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.database.Cursor;
import android.net.Uri;
import android.os.Build;
import android.provider.CallLog;
import android.provider.Settings;
import android.telephony.TelephonyManager;
import android.util.Log;
import androidx.core.content.ContextCompat;
import com.getcapacitor.JSObject;
import com.getcapacitor.Plugin;
import com.getcapacitor.PluginCall;
import com.getcapacitor.PluginMethod;
import com.getcapacitor.annotation.CapacitorPlugin;
import com.getcapacitor.annotation.Permission;
import com.getcapacitor.annotation.PermissionCallback;

@CapacitorPlugin(
    name = "CallDetection",
    permissions = {
        @Permission(
            strings = {
                Manifest.permission.READ_PHONE_STATE,
                Manifest.permission.READ_CALL_LOG
            },
            alias = "phone"
        )
    }
)
public class CallDetectionPlugin extends Plugin {
    private static final String PERMISSION_ALIAS = "phone";

    @PluginMethod
    public void requestPermissions(PluginCall call) {
        requestPermissionForAlias(PERMISSION_ALIAS, call, "phonePermsCallback");
    }

    @PermissionCallback
    private void phonePermsCallback(PluginCall call) {
        if (hasRequiredPermissions()) {
            call.resolve();
        } else {
            call.reject("Phone permissions are required");
        }
    }

    public boolean hasRequiredPermissions() {
        return hasPermission(Manifest.permission.READ_PHONE_STATE) &&
               hasPermission(Manifest.permission.READ_CALL_LOG);
    }

    /**
     * Check if overlay permission (Display over other apps) is granted.
     * Required for Android 10+ to open app from background.
     * This permission must be granted manually by user from Settings.
     */
    @PluginMethod
    public void checkOverlayPermission(PluginCall call) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (!Settings.canDrawOverlays(getContext())) {
                // Open settings to grant overlay permission
                Intent intent = new Intent(
                    Settings.ACTION_MANAGE_OVERLAY_PERMISSION,
                    Uri.parse("package:" + getContext().getPackageName())
                );
                getActivity().startActivityForResult(intent, 1234);
                call.reject("Overlay permission required. Please enable 'Display over other apps' in Settings.");
            } else {
                JSObject result = new JSObject();
                result.put("granted", true);
                call.resolve(result);
            }
        } else {
            // Below Android 6.0, overlay permission is not needed
            JSObject result = new JSObject();
            result.put("granted", true);
            call.resolve(result);
        }
    }

    /**
     * Check if overlay permission is granted (without opening settings)
     */
    @PluginMethod
    public void hasOverlayPermission(PluginCall call) {
        boolean granted = false;
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            granted = Settings.canDrawOverlays(getContext());
        } else {
            granted = true; // Not needed below Android 6.0
        }
        
        JSObject result = new JSObject();
        result.put("granted", granted);
        call.resolve(result);
    }

    /**
     * Get the last incoming call number from call log
     */
    @PluginMethod
    public void getLastIncomingCallNumber(PluginCall call) {
        // Check permission first
        if (!hasPermission(Manifest.permission.READ_CALL_LOG)) {
            call.reject("READ_CALL_LOG permission is required");
            return;
        }

        try {
            ContentResolver resolver = getContext().getContentResolver();
            String[] projection = {
                CallLog.Calls.NUMBER,
                CallLog.Calls.TYPE,
                CallLog.Calls.DATE,
                CallLog.Calls.DURATION,
                CallLog.Calls.CACHED_NAME
            };
            String selection = CallLog.Calls.TYPE + " = ?";
            String[] selectionArgs = {String.valueOf(CallLog.Calls.INCOMING_TYPE)};
            String sortOrder = CallLog.Calls.DATE + " DESC LIMIT 1";

            Cursor cursor = resolver.query(
                CallLog.Calls.CONTENT_URI,
                projection,
                selection,
                selectionArgs,
                sortOrder
            );

            JSObject result = new JSObject();
            
            if (cursor != null && cursor.moveToFirst()) {
                int numberIndex = cursor.getColumnIndex(CallLog.Calls.NUMBER);
                int dateIndex = cursor.getColumnIndex(CallLog.Calls.DATE);
                int durationIndex = cursor.getColumnIndex(CallLog.Calls.DURATION);
                int nameIndex = cursor.getColumnIndex(CallLog.Calls.CACHED_NAME);

                if (numberIndex >= 0) {
                    String number = cursor.getString(numberIndex);
                    long date = dateIndex >= 0 ? cursor.getLong(dateIndex) : 0;
                    int duration = durationIndex >= 0 ? cursor.getInt(durationIndex) : 0;
                    String name = nameIndex >= 0 ? cursor.getString(nameIndex) : null;

                    result.put("number", number != null ? number : "");
                    result.put("date", date);
                    result.put("duration", duration);
                    result.put("name", name != null ? name : "");
                    result.put("found", true);
                    
                    Log.d("CallDetectionPlugin", "Found last incoming call: " + number);
                } else {
                    result.put("found", false);
                    result.put("number", "");
                }
                cursor.close();
            } else {
                result.put("found", false);
                result.put("number", "");
            }

            call.resolve(result);
        } catch (Exception e) {
            Log.e("CallDetectionPlugin", "Error reading call log: " + e.getMessage());
            call.reject("Failed to read call log: " + e.getMessage());
        }
    }
}

