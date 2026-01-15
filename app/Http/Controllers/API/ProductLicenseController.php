<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\ProductLicenseRepository;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ProductLicenseController extends Controller
{
    public function downloadLicensePdf($license,Request $request){
        $licenseData = ProductLicenseRepository::query()->where('product_license', $license)->where('order_id', $request->order_id)->first();

        $setting = generaleSetting('setting');
        $logo = $setting->logo;

        $html = view('PDF.license', compact('licenseData','logo'))->render();

        // Create mPDF instance
        $mpdf = new Mpdf([
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'margin_right' => 15,
            'default_font_size' => 12,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);

        // Output as download
        return response($mpdf->Output("license-{$licenseData->user?->name}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="license-'.$licenseData->user?->name.'.pdf"');
    }
}
