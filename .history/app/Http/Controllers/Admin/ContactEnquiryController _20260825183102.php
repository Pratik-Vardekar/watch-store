<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Illuminate\Http\Request;

class ContactEnquiryController extends Controller
{
    public function index()
    {
        $contactEnquiries = ContactEnquiry::orderBy('id', 'DESC')->get();

        return view('admin.contact_enquiries.index', compact('contactEnquiries'));
    }

    public function edit(ContactEnquiry $contactEnquiry)
    {
        return view('admin.contact_enquiries.edit', compact('contactEnquiry'));
    }


    public function update(Request $request, ContactEnquiry $contactEnquiry)
    {
        $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'contact_no' => 'required',
            'comment'    => 'nullable|string',
            'status'     => 'required'
        ]);

        $contactEnquiry->update([
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'contact_no' => $request->contact_no,
            'comment'    => $request->comment,
            'status'     => $request->status,
        ]);

        return redirect()
            ->route('admin.contact-enquiries.index')
            ->with('success','Contact enquiry updated successfully');
    }

    public function contactexport(Request $request)
    {
        $fileName = 'contact_enquiries_' . date('Y-m-d_H-i-s') . '.csv';
    
        $contacts = ContactEnquiry::orderBy('id', 'desc')->get();
    
        return response()->streamDownload(function () use ($contacts) {
    
            $file = fopen('php://output', 'w');
    
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
            // CSV Header
            fputcsv($file, [
                'ID',
                'Full Name',
                'Email',
                'Contact No',
                'Comment',
                'Status',
                'Created At',
            ]);
    
            // Contact enquiry data
            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->full_name,
                    $contact->email,
                    $contact->contact_no,
                    $contact->comment,
                    $contact->status ? 'Active' : 'Inactive',
                    $contact->created_at,
                ]);
            }
    
            fclose($file);
    
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}