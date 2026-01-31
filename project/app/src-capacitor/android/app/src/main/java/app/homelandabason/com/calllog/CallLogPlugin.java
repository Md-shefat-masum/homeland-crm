package app.homelandabason.com.calllog;

import android.Manifest;
import android.database.Cursor;
import android.provider.CallLog;
import androidx.core.content.ContextCompat;
import com.getcapacitor.JSArray;
import com.getcapacitor.JSObject;
import com.getcapacitor.Plugin;
import com.getcapacitor.PluginCall;
import com.getcapacitor.PluginMethod;
import com.getcapacitor.annotation.CapacitorPlugin;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashSet;
import java.util.Locale;
import java.util.Set;

@CapacitorPlugin(name = "CallLog")
public class CallLogPlugin extends Plugin {

    @PluginMethod()
    public void get_call_log(PluginCall call) {
        // Check permission first
        if (ContextCompat.checkSelfPermission(getContext(), Manifest.permission.READ_CALL_LOG) != android.content.pm.PackageManager.PERMISSION_GRANTED) {
            call.reject("READ_CALL_LOG permission is required");
            return;
        }
        JSArray callLogList = new JSArray();
        Set<String> uniqueNumbers = new HashSet<>();
        SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());

        String[] projection = new String[] {
            CallLog.Calls.CACHED_NAME,
            CallLog.Calls.NUMBER,
            CallLog.Calls.DATE
        };

        try (Cursor cursor = getContext().getContentResolver().query(
                CallLog.Calls.CONTENT_URI,
                projection,
                null,
                null,
                CallLog.Calls.DATE + " DESC"
        )) {
            if (cursor != null) {
                int nameIndex = cursor.getColumnIndex(CallLog.Calls.CACHED_NAME);
                int numberIndex = cursor.getColumnIndex(CallLog.Calls.NUMBER);
                int dateIndex = cursor.getColumnIndex(CallLog.Calls.DATE);

                while (cursor.moveToNext() && callLogList.length() < 10) {
                    String number = cursor.getString(numberIndex);
                    
                    if (number == null || number.isEmpty() || uniqueNumbers.contains(number)) {
                        continue;
                    }

                    String name = cursor.getString(nameIndex);
                    long callDateMillis = cursor.getLong(dateIndex);
                    String formattedDate = dateFormat.format(new Date(callDateMillis));

                    JSObject callObj = new JSObject();
                    callObj.put("name", (name != null && !name.isEmpty()) ? name : "Unknown");
                    callObj.put("number", number);
                    callObj.put("time", formattedDate);
                    
                    callLogList.put(callObj);
                    uniqueNumbers.add(number);
                }
            }
        } catch (Exception e) {
            call.reject("Error reading call log: " + e.getLocalizedMessage());
            return;
        }

        JSObject ret = new JSObject();
        ret.put("calls", callLogList);
        call.resolve(ret);
    }
}
