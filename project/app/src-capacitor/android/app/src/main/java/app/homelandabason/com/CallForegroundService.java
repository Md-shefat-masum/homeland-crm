package app.homelandabason.com;

import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.Service;
import android.content.Intent;
import android.os.Build;
import android.os.IBinder;
import android.util.Log;

import androidx.core.app.NotificationCompat;

import android.app.PendingIntent;

public class CallForegroundService extends Service {
    private static final String TAG = "CallForegroundService";

    public static final String ACTION_START = "app.homelandabason.com.CALL_FG_START";
    public static final String ACTION_STOP  = "app.homelandabason.com.CALL_FG_STOP";

    private static final String CHANNEL_ID = "incoming_call_channel";
    private static final int NOTIFICATION_ID = 9911;

    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        String action = intent != null ? intent.getAction() : null;
        if (ACTION_STOP.equals(action)) {
            Log.d(TAG, "Stopping foreground service");
            stopForeground(true);
            stopSelf();
            return START_NOT_STICKY;
        }

        String phoneNumber = intent != null ? intent.getStringExtra("phone_number") : null;
        Log.d(TAG, "Starting foreground service. phone_number=" + phoneNumber);

        ensureChannel();

        Intent openIntent = new Intent(this, MainActivity.class);
        openIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        openIntent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        openIntent.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP);
        openIntent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
        openIntent.addFlags(Intent.FLAG_ACTIVITY_RESET_TASK_IF_NEEDED);
        openIntent.putExtra("from_call", true);
        openIntent.putExtra("force_open", true);
        if (phoneNumber != null) {
            openIntent.putExtra("phone_number", phoneNumber);
        }

        PendingIntent pendingIntent = PendingIntent.getActivity(
            this,
            9911,
            openIntent,
            (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M
                ? PendingIntent.FLAG_UPDATE_CURRENT | PendingIntent.FLAG_IMMUTABLE
                : PendingIntent.FLAG_UPDATE_CURRENT)
        );

        String contentText = (phoneNumber != null && !phoneNumber.isEmpty())
            ? ("Phone: " + phoneNumber)
            : "Open customer create";

        Notification notification = new NotificationCompat.Builder(this, CHANNEL_ID)
            .setSmallIcon(getApplicationInfo().icon)
            .setContentTitle("Incoming call")
            .setContentText(contentText)
            .setCategory(NotificationCompat.CATEGORY_CALL)
            .setPriority(NotificationCompat.PRIORITY_MAX)
            .setOngoing(true)
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)
            // Most reliable way to bring activity to front on many Android 10+ devices
            .setFullScreenIntent(pendingIntent, true)
            .build();

        startForeground(NOTIFICATION_ID, notification);

        // Also attempt direct open immediately (some devices allow it)
        try {
            startActivity(openIntent);
        } catch (Exception e) {
            Log.w(TAG, "startActivity blocked/failed: " + e.getMessage());
        }

        return START_NOT_STICKY;
    }

    private void ensureChannel() {
        if (Build.VERSION.SDK_INT < Build.VERSION_CODES.O) return;

        NotificationManager nm = (NotificationManager) getSystemService(NOTIFICATION_SERVICE);
        if (nm == null) return;

        NotificationChannel existing = nm.getNotificationChannel(CHANNEL_ID);
        if (existing != null) return;

        NotificationChannel channel = new NotificationChannel(
            CHANNEL_ID,
            "Incoming calls",
            NotificationManager.IMPORTANCE_HIGH
        );
        channel.setDescription("Used to show full-screen incoming call notification to open CRM");
        channel.setLockscreenVisibility(Notification.VISIBILITY_PUBLIC);
        nm.createNotificationChannel(channel);
    }
}


