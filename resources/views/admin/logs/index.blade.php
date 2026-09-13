@extends('layouts.admin')

@section('title', 'Log Sistem - Raso Mandeh')
@section('header_title', 'Log Sistem Administrator')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50 flex items-center justify-between">
            <h3 class="font-bold text-neutral-800 text-sm font-serif">Riwayat Aktivitas Sistem</h3>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left text-xs table-fixed">
                <thead class="bg-neutral-50/70 border-b border-neutral-200 text-[10px] uppercase tracking-widest text-neutral-500 font-serif">
                    <tr>
                        <th class="w-[18%] px-4 py-3.5 font-bold">Waktu</th>
                        <th class="w-[18%] px-4 py-3.5 font-bold">Pengguna</th>
                        <th class="w-[14%] px-4 py-3.5 font-bold">Aksi</th>
                        <th class="w-[35%] px-4 py-3.5 font-bold">Detail</th>
                        <th class="w-[15%] px-4 py-3.5 font-bold">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="px-4 py-3 text-neutral-600 text-xs">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3 truncate">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-full bg-[#7A1F2B]/10 text-[#7A1F2B] font-bold text-[10px] flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="font-semibold text-neutral-800 truncate">{{ $log->user->name ?? 'Sistem' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide
                                {{ $log->action == 'Login' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-neutral-600 text-xs truncate" title="{{ $log->description }}">
                            {{ $log->description }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-neutral-500 text-xs font-mono">
                            {{ $log->ip_address }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-neutral-400">
                            Belum ada log sistem.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <x-admin-pagination :paginator="$logs" entity="Log" />
    </div>
</div>
@endsection
