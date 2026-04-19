<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function revenueReport($subscriberId, $from, $to, $groupBy = 'day'): Collection
    {
        $dateFormat = match ($groupBy) {
            'month' => '%Y-%m',
            'year'  => '%Y',
            default => '%Y-%m-%d',
        };

        return DB::table('orders')
            ->selectRaw("
            DATE_FORMAT(created_at, ?) as period,
            COUNT(*) as orders_count,
            SUM(cost) as total_revenue,
            SUM(paid) as total_paid,
            SUM(cost - paid) as total_due
        ", [$dateFormat])
            ->where('subscriber_id', $subscriberId)
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'cancelled')
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }
    public function doctorsDue($subscriberId,$doctorId = null): Collection
    {
        return DB::table('orders')
            ->join('doctors', 'orders.doctor_id', '=', 'doctors.id')
            ->when($doctorId, function ($q) use ($doctorId) {
                $q->where('orders.doctor_id', $doctorId);
            })
            ->selectRaw("
            doctors.id as doctor_id,
            CONCAT(doctors.first_name,' ',doctors.last_name) as doctor_name,
            SUM(orders.cost) as total_cost,
            SUM(orders.paid) as total_paid,
            SUM(orders.cost - orders.paid) as total_due
        ")
            ->where('orders.subscriber_id', $subscriberId)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('doctors.id','doctors.first_name','doctors.last_name')
            ->havingRaw('SUM(orders.cost - orders.paid) > 0')
            ->orderByDesc('total_due')
            ->get();
    }
    public function clinicDoctorsDue($subscriberId, $clinicId): Collection
    {
        return DB::table('orders')
            ->join('doctors', 'orders.doctor_id', '=', 'doctors.id')

            ->where('orders.subscriber_id', $subscriberId)
            ->where('doctors.clinic_id', $clinicId)
            ->where('orders.status', '!=', 'cancelled')

            ->selectRaw("
            doctors.id as doctor_id,
            CONCAT(doctors.first_name,' ',doctors.last_name) as doctor_name,
            SUM(orders.cost) as total_cost,
            SUM(orders.paid) as total_paid,
            SUM(orders.cost - orders.paid) as total_due
        ")

            ->groupBy('doctors.id','doctors.first_name','doctors.last_name')
            ->havingRaw('SUM(orders.cost - orders.paid) > 0')
            ->orderByDesc('total_due')
            ->get();
    }
}
