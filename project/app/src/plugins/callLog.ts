import { registerPlugin } from '@capacitor/core';

export interface CallLogEntry {
  name: string;
  number: string;
  time: string;
}

export interface CallLogPlugin {
  get_call_log(): Promise<{ calls: CallLogEntry[] }>;
}

export const CallLog = registerPlugin<CallLogPlugin>('CallLog');

