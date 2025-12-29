<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Letter;
use App\Models\Generated_letter;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Auth;

class LetterController extends Controller
{
    public function index()
    {
        return view('letter.list');
    }

    public function load(Request $request)
    {
        try {
            $draw = intval($request->get('draw', 0));
            $start = intval($request->get('start', 0));
            $length = intval($request->get('length', 10));
            $searchValue = $request->input('search.value', '');

            $query = Letter::query();
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('title', 'like', "%{$searchValue}%");
                });
            }
            $recordsTotal = Letter::count();
            $recordsFiltered = $query->count();
            $rows = $query->offset($start)->limit($length)->orderBy('id', 'desc')->get();

            $formattedData = [];
            foreach ($rows as $index => $row) {
                $actions = '<div class="edit-delete-action">';
                    $actions .= '<a href="'.route('admin.letter.generate',['letter_id' => $row->id]).'" class="me-2 edit-icon p-2" title="Generate Letter">Generate Letter</a>';
                    $actions .= '<a href="'.url('letters/'.$row->id).'" class="me-2 edit-icon p-2" title="View">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                    $actions .= '<a href="' . url('clients/'.$row->id.'/edit/') . '" class="me-2 edit-icon p-2 text-success" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>';
                    $actions .= '<a href="javascript:;" onclick="remove_row(\'' . url('clients/' . $row->id) . '\')" data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2" title="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </a>';
                $actions .= '</div>';
                $formattedData[] = [
                    'id' => $start + $index + 1,
                    'title' => $row->title,
                    'total_letters' => $row->total_letters,
                    'status' => $row->is_active
                        ? '<span class="badge badge-success badge-xs d-inline-flex align-items-center">Active</span>'
                        : '<span class="badge badge-danger badge-xs d-inline-flex align-items-center">Inactive</span>',
                    'actions' => $actions
                ];
            }
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        $letter = null;
        return view('letter.add_edit',compact('letter'));
    }

    public function store(Request $request)
    {
        try {
            $post = $request->all();
            
            $doc_file = "";
            if ($request->hasFile('file')) {
                $doc = $request->file('file');

                // generate random file name
                $doc_file = Str::random(20) . '.' . $doc->getClientOriginalExtension();
                $path = $doc->move(public_path('uploads/docs'), $doc_file);
            }

            $row = new Letter;
            $row->title = $post['title'];
            $row->description = $post['description'] ?? '';
            $row->file = $doc_file;
            $row->is_active = $post['is_active'];
            $row->created_by = Auth::user()->id;
            $row->created_at = date("Y-m-d H:i:s");
            $row->save();

            return response()->json(['success' => true,'message' => "Letter added successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function show($id)
    {
        $letter = Letter::find($id);
        if(!$letter) {
            return redirect("letters");
        }
        return view('letter.generated_letters',compact('letter'));   
    }

    public function load_generated_letters(Request $request)
    {
        try {
            $draw = intval($request->get('draw', 0));
            $start = intval($request->get('start', 0));
            $length = intval($request->get('length', 10));
            $searchValue = $request->input('search.value', '');
            $letter_id = $request->input('letter_id', '');

            $query = Generated_letter::with([
                'client' => function ($q) {
                    $q->select('id', 'AO1_Name'); // specify needed columns
                }
            ])
            ->where('letter_id', $letter_id);
            if (!empty($searchValue)) {
                $query->whereHas('client', function ($q) use ($searchValue) {
                    $q->where('AO1_Name', 'like', "%{$searchValue}%");
                });
            }
            $recordsTotal = Client::with('generatedLetters')->where('letter_id',$letter_id);
            $recordsFiltered = $query->count();
            $rows = $query->offset($start)->limit($length)->orderBy('id', 'desc')->get();

            $formattedData = [];
            foreach ($rows as $index => $row) {
                $actions = '<div class="edit-delete-action">';
                    $actions .= '<a href="'.route('admin.letter.download',["letter_id" => $row->id]).'" class="me-2 edit-icon p-2" title="Download">Download</a>';
                    $actions .= '<a href="javascript:;" onclick="remove_row(\'' . url('clients/' . $row->id) . '\')" data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2" title="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </a>';
                $actions .= '</div>';
                $formattedData[] = [
                    'id' => $start + $index + 1,
                    'client' => $row->client?->AO1_Name ?? '',
                    'created_on' => format_date($row->created_at),
                    'actions' => $actions
                ];
            }
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function generate_letter($id)
    {
        $letter = Letter::find($id);
        if(!$letter) {
            return redirect("letters");
        }
        $clients = Client::where("is_active",1)->get();
        if(!$clients->isEmpty()) {
            $insert_data = [];
            foreach ($clients as $client) {
                $template = new TemplateProcessor(storage_path('app/Templates/letter.docx'));
                $template->setValue('{Property_Address}', $client->AO1_Name);
                $fileName = 'letter_'.$id.'_'.$client->id.'.docx';
                $template->saveAs(public_path('uploads/letters/' . $fileName));

                $insert_data[] = array(
                    'client_id' => $client->id,
                    'letter_id' => $id,
                    'file' => $fileName,
                    'created_by' => Auth::user()->id,
                    'created_at' => date("Y-m-d H:i:s")
                );
            }
            $generated_letter = new Generated_letter;
            $generated_letter->insert($insert_data);       

            $letter = Letter::find($id);
            $letter->total_letters = count($insert_data);
            $letter->save();
        }
        return redirect("letters");   
    }

    public function edit($id)
    {
        $site = Site::find($id);
        if(!$site) {
            return redirect()->route("admin.dashboard");
        }
        return view('admin.site.add_edit',compact('site'));   
    }

    public function update(Request $request,$id)
    {
        try {
            $post = $request->all();

            $row = Site::find($id);
            $row->project_no = $post['project_no'];
            $row->project_type = $post['project_type'];
            $row->project_start_date = $post['project_start_date'];
            $row->project_status = $post['project_status'];
            $row->invoice_status = $post['invoice_status'];
            $row->address = $post['address'];
            $row->lat = $post['lat'] ?? 0;
            $row->lng = $post['lng'] ?? 0;
            $row->is_active = $post['is_active'];
            $row->updated_at = date("Y-m-d H:i:s");
            $row->save();

            return response()->json(['success' => true,'message' => "Site edited successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function destroy($id)
    {
        $row = Client::find($id);
        $row->deleted_by = Auth::user()->id;
        $row->save();

        Client::destroy($id);
        return response()->json(['success' => true,'message' => "Client removed successfully."], 200);
    }

    public function download($id)
    {
        $row = Generated_letter::select('file')->find($id);
        if(!$row) {
            abort(404, 'Letter not found'); 
        }
        $path = public_path('uploads/letters/' . $row->file);
        if(!file_exists($path)) {
            abort(404, 'File not found');
        }
        return response()->download($path);
    }
}
