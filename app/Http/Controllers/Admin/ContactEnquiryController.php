<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\Concerns\HandlesCsv;

class ContactEnquiryController extends Controller
{
    use HandlesCsv;

    public function index()
    {
        $contactEnquiries = ContactEnquiry::orderBy('id', 'DESC')->get();

        return view('admin.contact_enquirey.index', compact('contactEnquiries'));
    }

    public function contactexport(Request $request)
    {
        $fileName = 'contact_enquiries_' . date('Y-m-d_His') . '.csv';

        $query = ContactEnquiry::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_no', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $enquiries = $query->orderBy('id', 'DESC')->get();

        $headers = [
            'ID',
            'Full Name',
            'Email',
            'Contact No',
            'Comment',
            'Status',
            'Date',
            'Time'
        ];

        return $this->streamCsvDownload($fileName, $headers, function ($file) use ($enquiries) {
            foreach ($enquiries as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->full_name,
                    $item->email,
                    $item->contact_no,
                    $item->comment ?? '',
                    $item->status ? 'Read' : 'Unread',
                    $item->created_at ? $item->created_at->format('d-m-Y') : '',
                    $item->created_at ? $item->created_at->format('H:i:s') : '',
                ]);
            }
        });
    }

    public function export(Request $request)
    {
        return $this->contactexport($request);
    }

    public function edit(ContactEnquiry $contactEnquiry)
    {
        return view('admin.contact_enquirey.edit', compact('contactEnquiry'));
    }

    public function update(Request $request, ContactEnquiry $contactEnquiry)
    {
        $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'contact_no' => 'required',
            'comment'    => 'nullable|string',
            'status'     => 'required',
        ]);

        $contactEnquiry->update([
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'contact_no' => $request->contact_no,
            'comment'    => $request->comment,
            'status'     => (bool) $request->status,
        ]);

        return redirect()
            ->route('admin.contact-enquiries.index')
            ->with('success', 'Contact enquiry updated successfully');
    }
}
