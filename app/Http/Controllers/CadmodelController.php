<?php

namespace App\Http\Controllers;

use App\Cadmodel;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Amranidev\Ajaxis\Ajaxis;
use URL;

use Storage;
use Jenssegers\Agent\Agent;

/**
 * Class CadmodelController.
 *
 * @author  The scaffold-interface created at 2017-09-05 08:07:22pm
 * @link  https://github.com/amranidev/scaffold-interface
 */
class CadmodelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - cadmodel';
        $cadmodels = \Auth::user()->cadmodels()->orderByDesc('updated_at')->paginate(10);
        return view('cadmodel.index',compact('cadmodels','title'));
    }

    public function AllUserindex()
    {
        $title = 'All CAD Models';
        $cadmodels = Cadmodel::orderByDesc('updated_at')->paginate(10);
        return view('cadmodel.allmodels',compact('cadmodels','title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - cadmodel';
        
        return view('cadmodel.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request  $request
     * @return  \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cadmodel = new Cadmodel();


        
        $cadmodel->Name = $request->Name;

        
        $cadmodel->Description = $request->Description;

        
        $cadmodel->ModelFile = "";

        
        $cadmodel->Material = "";

        $cadmodel->user_id = \Auth::user()->id;

        $cadmodel->save();

        $pusher = App::make('pusher');

        //default pusher notification.
        //by default channel=test-channel,event=test-event
        //Here is a pusher notification example when you create a new resource in storage.
        //you can modify anything you want or use it wherever.
        $pusher->trigger('test-channel',
                         'test-event',
                        ['message' => 'A new cadmodel has been created !!']);

        return redirect('cadmodel/'.$cadmodel->id.'/edit');
    }

    /**
     * Display the specified resource.
     *
     * @param    \Illuminate\Http\Request  $request
     * @param    int  $id
     * @return  \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $agent = new Agent();
        $title = 'Show - cadmodel';

        if($request->ajax())
        {
            return URL::to('cadmodel/'.$id);
        }

        $cadmodel = Cadmodel::findOrfail($id);
        return view('cadmodel.show',compact('title','cadmodel', 'agent'));
    }


    public function showSTL($id, Request $request)
    {
        $title = 'Show - auto3dprintcue';

        if ($request->ajax()) {
            return URL::to('auto3dprintcue/' . $id);
        }


        $myyfileout = file_get_contents("../storage/app/3dCadModels/" . $id . ".stl");
        return response($myyfileout, 200)->header('Content-Type', 'application/octet-stream');
    }

    /**
     * Show the form for editing the specified resource.
     * @param    \Illuminate\Http\Request  $request
     * @param    int  $id
     * @return  \Illuminate\Http\Response
     */
    public function edit($id,Request $request)
    {
        $agent = new Agent();
        $title = 'Edit - cadmodel';
        if($request->ajax())
        {
            return URL::to('cadmodel/'. $id . '/edit');
        }

        
        $cadmodel = Cadmodel::findOrfail($id);
        return view('cadmodel.edit',compact('title','cadmodel' ,'agent' ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request  $request
     * @param    int  $id
     * @return  \Illuminate\Http\Response
     */

    public function updatemodel($id,Request $request)
    {
        $cadmodel = Cadmodel::findOrfail($id);
        $cadmodel->ModelFile = $request->ModelFile;
        $cadmodel->save();

        return redirect('cadmodel');
    }


    public function updatemodelSTL($id,Request $request)
    {

        Storage::disk('local')->put("3dCadModels\\" . $id. ".stl", $request->STLFile);



        return redirect('cadmodel');
    }



    public function update($id,Request $request)
    {
        $cadmodel = Cadmodel::findOrfail($id);
    	
        $cadmodel->Name = $request->Name;
        
        $cadmodel->Description = $request->Description;
        
        $cadmodel->ModelFile = $request->ModelFile;
        
        $cadmodel->Material = $request->Material;
        
        
        $cadmodel->save();

        return redirect('cadmodel');
    }

    /**
     * Delete confirmation message by Ajaxis.
     *
     * @link      https://github.com/amranidev/ajaxis
     * @param    \Illuminate\Http\Request  $request
     * @return  String
     */
    public function DeleteMsg($id,Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!','Would you like to remove This?','/cadmodel/'. $id . '/delete');

        if($request->ajax())
        {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
     	$cadmodel = Cadmodel::findOrfail($id);
     	$cadmodel->delete();
        return URL::to('cadmodel');
    }
}
