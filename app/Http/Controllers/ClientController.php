<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Imports\ClientsImport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;

class ClientController extends Controller
{
    public function index()
    {
        return view('client.list');
    }

    public function load(Request $request)
    {
        try {
            $draw = intval($request->get('draw', 0));
            $start = intval($request->get('start', 0));
            $length = intval($request->get('length', 10));
            $searchValue = $request->input('search.value', '');

            $query = Client::query();
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('AO1_Name', 'like', "%{$searchValue}%");
                    $q->where('BO1_Name', 'like', "%{$searchValue}%");
                });
            }
            $recordsTotal = Client::count();
            $recordsFiltered = $query->count();
            $rows = $query->offset($start)->limit($length)->orderBy('id', 'desc')->get();

            $formattedData = [];
            foreach ($rows as $index => $row) {
                $actions = '<div class="edit-delete-action">';
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
                    'AO1_Name' => $row->AO1_Name,
                    'BO1_Name' => $row->BO1_Name,
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

    public function downloadSample()
    {
        $path = public_path('krn_site_sample.csv');
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->download($path, 'sample.csv');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt'
        ]);
        Excel::import(new ClientsImport, $request->file('file'));

        return back()->with('success', 'Sites imported successfully!');
    }

    public function create()
    {
        $project = null;
        return view('admin.project.add_edit',compact('project'));
    }

    public function store(Request $request)
    {
        try {
            $post = $request->all();
            exit;
            $avatar = "";
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // generate random file name
                $avatar = Str::random(20) . '.' . $image->getClientOriginalExtension();
                $path = $image->move(public_path('uploads/project'), $avatar);
            }
            $after_avatar = "";
            if ($request->hasFile('after_image')) {
                $after_image = $request->file('after_image');

                // generate random file name
                $after_avatar = Str::random(20) . '.' . $after_image->getClientOriginalExtension();
                $path = $after_image->move(public_path('uploads/project'), $after_avatar);
            }

            $row = new Project;
            $row->name = $post['name'];
            $row->avatar = $avatar;
            $row->after_avatar = $after_avatar;
            $row->is_active = $post['is_active'];
            $row->created_at = date("Y-m-d H:i:s");
            $row->save();

            return response()->json(['success' => true,'message' => "Project added successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
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
}
