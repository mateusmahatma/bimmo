<?php

namespace App\Exports;

use App\Models\Aset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class AsetExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Aset::where('id_user', Auth::id())->get();
    }

    public function headings(): array
    {
        return [
            'Kode Aset',
            'Nama Aset',
            'Kategori',
            'Merk/Model',
            'Nomor Seri',
            'Tanggal Pembelian',
            'Harga Beli',
            'Masa Pakai (Tahun)',
            'Nilai Sisa',
            'Kondisi',
            'Lokasi',
            'PIC',
            'Status Disposal',
            'Tanggal Disposal',
            'Nilai Buku Saat Ini'
        ];
    }

    public function map($aset): array
    {
        return [
            $aset->kode_aset,
            $aset->nama_aset,
            $aset->kategori,
            $aset->merk_model,
            $aset->nomor_seri,
            $aset->tanggal_pembelian->format('d/m/Y'),
            $aset->harga_beli,
            $aset->masa_pakai,
            $aset->nilai_sisa,
            $aset->kondisi,
            $aset->lokasi,
            $aset->pic,
            $aset->is_disposed ? 'Terhapus/Terjual' : 'Aktif',
            $aset->tanggal_disposal ? $aset->tanggal_disposal->format('d/m/Y') : '-',
            $aset->nilai_buku
        ];
    }
}
