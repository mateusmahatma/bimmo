<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Auth;


class FinancialCalculatorController extends Controller
{
    public function index()
    {
        return view('kalkulator.index');
    }

    public function calculate(Request $request)
    {
        $userId = Auth::id();
        $query = Anggaran::where('id_user', $userId);

        $monthly_income = $request->input('monthly_income');
        $additional_income = $request->input('additional_income');
        $totalIncome = $monthly_income + $additional_income;

        $Anggaran = $query->get();

        $budgetAllocations = [];

        foreach ($Anggaran as $anggaran) {
            $budgetAllocations[] = [
                'nama_anggaran' => $anggaran->nama_anggaran,
                'persentase_anggaran' => $anggaran->persentase_anggaran,
                'nominal' => ($anggaran->persentase_anggaran / 100) * $totalIncome
            ];
        }

        $totalBudget = array_sum(array_column($budgetAllocations, 'nominal'));

        $remainingIncome = $totalIncome - $totalBudget;

        Session::put('budgetAllocations', $budgetAllocations);
        Session::put('totalBudget', $totalBudget);
        Session::put('totalIncome', $totalIncome);
        Session::put('remainingIncome', $remainingIncome);


        return view('kalkulator.result', [
            'totalIncome' => $totalIncome,
            'budgetAllocations' => $budgetAllocations,
            'totalBudget' => $totalBudget,
            'remainingIncome' => $remainingIncome
        ]);
    }

    public function showResult(Request $request)
    {
        $monthly_income = $request->input('monthly_income');
        $additional_income = $request->input('additional_income');
        $totalIncome = $monthly_income + $additional_income;

        $budgetAllocations = Session::get('budgetAllocations', function () use ($totalIncome) {
            $Anggaran = Anggaran::all();
            $allocations = [];
            foreach ($Anggaran as $anggaran) {
                $allocations[] = [
                    'nama_anggaran' => $anggaran->nama_anggaran,
                    'persentase_anggaran' => $anggaran->persentase_anggaran,
                    'nominal' => ($anggaran->persentase_anggaran / 100) * $totalIncome
                ];
            }
            return $allocations;
        });

        $totalBudget = Session::get('totalBudget', array_sum(array_column($budgetAllocations, 'nominal')));

        $totalIncome = Session::get('totalIncome', $totalIncome);

        $remainingIncome = $totalIncome - $totalBudget;

        return view('kalkulator.result', [
            'totalIncome' => $totalIncome,
            'budgetAllocations' => $budgetAllocations,
            'totalBudget' => $totalBudget,
            'remainingIncome' => $remainingIncome
        ]);
    }

    public function cetak_pdf(Request $request)
    {
        $monthly_income = $request->input('monthly_income');
        $additional_income = $request->input('additional_income');
        $totalIncome = $monthly_income + $additional_income;

        $budgetAllocations = Session::get('budgetAllocations', function () use ($totalIncome) {
            $Anggaran = Anggaran::all();
            $allocations = [];
            foreach ($Anggaran as $anggaran) {
                $allocations[] = [
                    'nama_anggaran' => $anggaran->nama_anggaran,
                    'persentase_anggaran' => $anggaran->persentase_anggaran,
                    'nominal' => ($anggaran->persentase_anggaran / 100) * $totalIncome
                ];
            }
            return $allocations;
        });

        $totalBudget = Session::get('totalBudget', array_sum(array_column($budgetAllocations, 'nominal')));

        $totalIncome = Session::get('totalIncome', $totalIncome);

        $remainingIncome = $totalIncome - $totalBudget;

        $data = [
            'totalIncome' => $totalIncome,
            'budgetAllocations' => $budgetAllocations,
            'totalBudget' => $totalBudget,
            'remainingIncome' => $remainingIncome
        ];
        $pdf = PDF::loadview('Kalkulator.pdf', $data);
        return $pdf->stream('');
    }
}
