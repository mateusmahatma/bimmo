<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemasukanTemplateExport;
use App\Imports\PemasukanCategoryImport;
use App\Exports\PengeluaranTemplateExport;
use App\Imports\PengeluaranCategoryImport;
use App\Exports\DompetTemplateExport;
use App\Imports\DompetImport;
use App\Exports\AsetTemplateExport;
use App\Imports\AsetImport;
use App\Exports\PinjamanTemplateExport;
use App\Imports\PinjamanImport;
use App\Exports\TujuanKeuanganTemplateExport;
use App\Imports\TujuanKeuanganImport;
use App\Exports\AnggaranTemplateExport;
use App\Imports\AnggaranImport;

class MigrationController extends Controller
{
    /**
     * Display the migration page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $features = [
            [
                'id' => 'pemasukan',
                'name' => 'Income',
                'description' => 'Migrate income records.',
                'icon' => 'bi-graph-up-arrow',
                'color' => 'success'
            ],
            [
                'id' => 'pengeluaran',
                'name' => 'Expense',
                'description' => 'Migrate expense records.',
                'icon' => 'bi-graph-down-arrow',
                'color' => 'danger'
            ],
            [
                'id' => 'dompet',
                'name' => 'Wallet',
                'description' => 'Migrate wallet balances and types.',
                'icon' => 'bi-wallet2',
                'color' => 'primary'
            ],
            [
                'id' => 'aset',
                'name' => 'Assets',
                'description' => 'Migrate fixed asset list.',
                'icon' => 'bi-box-seam',
                'color' => 'info'
            ],
            [
                'id' => 'pinjaman',
                'name' => 'Liability',
                'description' => 'Migrate loan history.',
                'icon' => 'bi-arrow-down-up',
                'color' => 'warning'
            ],
            [
                'id' => 'tujuan_keuangan',
                'name' => 'Financial Goals',
                'description' => 'Migrate future targets.',
                'icon' => 'bi-bullseye',
                'color' => 'secondary'
            ],
            [
                'id' => 'anggaran',
                'name' => 'Budget',
                'description' => 'Migrate budget categories.',
                'icon' => 'bi-grid-fill',
                'color' => 'dark'
            ],
        ];

        return view('user-guide.migration', [
            'title' => __('Migrate to Bimmo'),
            'features' => $features
        ]);
    }

    /**
     * Handle template download.
     */
    public function downloadTemplate($type)
    {
        if ($type === 'pemasukan') {
            return Excel::download(new PemasukanTemplateExport, 'template_migrasi_pemasukan.xlsx');
        }

        if ($type === 'pengeluaran') {
            return Excel::download(new PengeluaranTemplateExport, 'template_migrasi_pengeluaran.xlsx');
        }

        if ($type === 'dompet') {
            return Excel::download(new DompetTemplateExport, 'template_migrasi_dompet.xlsx');
        }

        if ($type === 'aset') {
            return Excel::download(new AsetTemplateExport, 'template_migrasi_aset.xlsx');
        }

        if ($type === 'pinjaman') {
            return Excel::download(new PinjamanTemplateExport, 'template_migrasi_pinjaman.xlsx');
        }

        if ($type === 'tujuan_keuangan') {
            return Excel::download(new TujuanKeuanganTemplateExport, 'template_migrasi_tujuan_keuangan.xlsx');
        }

        if ($type === 'anggaran') {
            return Excel::download(new AnggaranTemplateExport, 'template_migrasi_anggaran.xlsx');
        }

        // Placeholder for other template generation
        return response()->json(['message' => 'Template for ' . $type . ' will be downloaded soon.']);
    }

    /**
     * Handle file upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if ($request->type === 'pemasukan') {
            try {
                Excel::import(new PemasukanCategoryImport, $request->file('file'));
                return back()->with([
                    'success' => __('Income data successfully uploaded and saved.'),
                    'redirect_url' => route('pemasukan.index'),
                    'redirect_name' => __('Income')
                ]);
            }
            catch (\Exception $e) {
                return back()->with('error', __('An error occurred while importing data:') . ' ' . $e->getMessage());
            }
        }

        if ($request->type === 'pengeluaran') {
            try {
                Excel::import(new PengeluaranCategoryImport, $request->file('file'));
                return back()->with([
                    'success' => __('Expense data successfully uploaded and saved.'),
                    'redirect_url' => route('pengeluaran.index'),
                    'redirect_name' => __('Expense')
                ]);
            }
            catch (\Exception $e) {
                return back()->with('error', __('An error occurred while importing data:') . ' ' . $e->getMessage());
            }
        }

        if ($request->type === 'dompet') {
            try {
                Excel::import(new DompetImport, $request->file('file'));
                return back()->with([
                    'success' => __('Wallet data successfully uploaded and saved.'),
                    'redirect_url' => route('dompet.index'),
                    'redirect_name' => __('Wallet')
                ]);
            }
            catch (\Exception $e) {
                return back()->with('error', __('An error occurred while importing data:') . ' ' . $e->getMessage());
            }
        }

        if ($request->type === 'aset') {
            try {
                Excel::import(new AsetImport, $request->file('file'));
                return back()->with([
                    'success' => __('Asset data successfully uploaded and saved.'),
                    'redirect_url' => route('aset.index'),
                    'redirect_name' => __('Assets')
                ]);
            }
            catch (\Exception $e) {
                return back()->with('error', __('An error occurred while importing data:') . ' ' . $e->getMessage());
            }
        }

        if ($request->type === 'pinjaman') {
            try {
                Excel::import(new PinjamanImport, $request->file('file'));
                return back()->with([
                    'success' => __('Liabilities & Debt data successfully uploaded and saved.'),
                    'redirect_url' => route('pinjaman.index'),
                    'redirect_name' => __('Liability')
                ]);
            }
            catch (\Exception $e) {
                return back()->with('error', __('An error occurred while importing data:') . ' ' . $e->getMessage());
            }
        }

        if ($request->type === 'tujuan_keuangan') {
            try {
                Excel::import(new TujuanKeuanganImport, $request->file('file'));
                return back()->with([
                    'success' => __('Financial Goals data successfully uploaded and saved.'),
                    'redirect_url' => route('tujuan-keuangan.index'),
                    'redirect_name' => __('Financial Goals')
                ]);
            }
            catch (\Exception $e) {
                return back()->with('error', __('An error occurred while importing data:') . ' ' . $e->getMessage());
            }
        }

        if ($request->type === 'anggaran') {
            try {
                Excel::import(new AnggaranImport, $request->file('file'));
                return back()->with([
                    'success' => __('Budget data successfully uploaded and saved.'),
                    'redirect_url' => route('anggaran.index'),
                    'redirect_name' => __('Budget')
                ]);
            }
            catch (\Exception $e) {
                return back()->with('error', __('An error occurred while importing data:') . ' ' . $e->getMessage());
            }
        }

        // Placeholder for file processing
        return back()->with('success', 'File berhasil diunggah.');
    }
}
