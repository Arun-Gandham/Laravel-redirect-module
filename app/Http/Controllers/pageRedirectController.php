<?php

namespace App\Http\Controllers;

use App\Models\pageRedirectModal;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class pageRedirectController extends Controller
{

    public function getRedirectUrlsDatatable() // function to get all redirect links from database and show in datatable
    {
        return view('redirectList');
    }

    public function createPageredirect(Request $request) // function to create the redirect link
    {
        try{
            $ifExistUrl = pageRedirectModal::where("from",$request->from_url)->get();
            if(isset($ifExistUrl[0]))
            {
                return response()->json(["status" => "200","message" => "Redirection alredy exists for this url"],200);
            }
            $newRedirectUrl = new pageRedirectModal();
            $newRedirectUrl->from = $request->from_url;
            $newRedirectUrl->to = $request->to_url;
            $newRedirectUrl->match_type = $request->match_type;
            $newRedirectUrl->save();
            $newRedirectUrl->dateFormated = date("F d, Y", strtotime($newRedirectUrl->updated_at));
            return response()->json(["status" => "201","message" => "Created sucessfully","insertedData"=>$newRedirectUrl],201);
        }
        catch(Exception $err)
        {
            return response()->json(["status" => "500","message" => $err->getMessage()],500);
        }
    }

    public function toggleLinkDisable(Request $request)  // function to tooggle the diable link
    {
        try{
            pageRedirectModal::where("id",(int) $request->id)->update(["is_disabled" => $request->disable_val]);
            return response()->json(["status" => "200","message" => "Updated sucessfully"],200);
        }
        catch (Exception $err)
        {
            return response()->json(["status" => "500","message" => $err->getMessage()],500);
        }
    }

    public function deleteRedirectLink(Request $request) // funciton to delete the delete redirect link
    {
        try{
            pageRedirectModal::where("id",(int) $request->id)->delete();
            return response()->json(["status" => "200","message" => "Deleted sucessfully"],200);
        }
        catch (Exception $err)
        {
            return response()->json(["status" => "500","message" => $err->getMessage()],500);
        }
    }

    public function updateRedirectLink(Request $request) // Update rdirect link
    {
        try{
            $newRedirectUrl = pageRedirectModal::find($request->id);
            $newRedirectUrl->from = $request->from_url;
            $newRedirectUrl->to = $request->to_url;
            $newRedirectUrl->match_type = $request->match_type;
            $newRedirectUrl->update();
            $newRedirectUrl->updated_at = date("F d, Y", strtotime($newRedirectUrl->updated_at));
            $newRedirectUrl->dateFormated = date("F d, Y", strtotime($newRedirectUrl->updated_at));
            return response()->json(["status" => "200","message" => "Updated sucessfully","insertedData"=>$newRedirectUrl],200);
        }
        catch (Exception $err)
        {
            return response()->json(["status" => "500","message" => $err->getMessage()],500);
        }
    }

    public function bulkActionUpdateRedirectLink(Request $request) // Bulk action update
    {
        try{
            
            $updateColumn = [];
            switch($request->bulk_action)
            {
                case "Enable":
                    $updateColumn['is_disabled'] = "0";
                    break;
                case "Disable":
                    $updateColumn['is_disabled'] = "1";
                    break;
                case "Delete":
                    pageRedirectModal::whereIn('id', $request->selected_checkboxes)->delete();
                    return response()->json(["status" => "200","message" => "Deleted sucessfully"],200);
                    break;
                default :
                    return response()->json(["status" => "200","message" => "Undefined request"],200);
                    break;
            }
            if(count($updateColumn))
            {
                pageRedirectModal::whereIn('id', $request->selected_checkboxes)->update($updateColumn);
                return response()->json(["status" => "200","message" => "Updated sucessfully"],200);
            }
            return response()->json(["status" => "404","message" => "Ids not found"],200);
        }
        catch (Exception $err)
        {
            return response()->json(["status" => "500","message" => $err->getMessage()],500);
        }
        
    }

    public function getRedirectUrls()
    {
        $pageRedirectUrls = pageRedirectModal::select('*')->orderBy('id',"DESC")->get();
        return Datatables::of($pageRedirectUrls)->addIndexColumn()
                ->addColumn('checkboxes', function($row){
                    $btn = '<td class="active">
                                 <input type="checkbox" class="select-item checkbox" name="select-item" value="'.$row->id.'" />
                             </td>';
                    return $btn;
                })
                ->addColumn('from_data', function($row){
                    $isEnabled = $row->is_disabled == "1" ? "inline" : "none";
                    $isDisabled = $row->is_disabled == "0" ? "inline" : "none";
                    $btn = ' <td class="success">
                    <p class="mb-1"><a href="'.url($row->from).'">'.$row->from.'</a></p>
                    <small>'.$row->to.'</small>
                    <div class="mt-1">
                        <a class="btn btn-link p-0 m-0 fs-10 link-primary text-decoration-none" onclick="toggleEditForm(`'.$row->id.'`,`'.$row->from.'`,`'.$row->to.'`)" >Edit</a> |
                        <a class="btn btn-link p-0 m-0 fs-10  link-primary text-decoration-none delete_redirect_link" onclick="delteRediretcLink('.$row->id.')">Delete</a> |
                        <a class="btn btn-link p-0 m-0 fs-10 enable_disable_link link-danger disable_link_'.$row->id.'" onclick="toggleDisable(1,'.$row->id.')" style="display:'.$isDisabled.'" data-disable="1" data-id="'.$row->id.'">Disable</a>
                        <a class="btn btn-link p-0 m-0 fs-10 enable_disable_link link-success enable_link_'.$row->id.'"  onclick="toggleDisable(0,'.$row->id.')"  style="display:'.$isEnabled.'" data-disable="0" data-id="'.$row->id.'">Enable</a>
                        </div>
                    </td>';
                    return $btn;
                })
                ->addColumn('formated_lastseen', function($row){
                    return date("F d, Y", strtotime($row->updated_at));
                })
                ->rawColumns(['checkboxes','from_data'])
                ->make(true);
    }

}
