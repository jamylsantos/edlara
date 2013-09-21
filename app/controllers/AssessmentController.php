<?php

class AssessmentController extends BaseController {

	public function siteview($id){
		return View::make('dashboard.assessments.view');
	}
	public function dashview($id){
		return;
	}
	public function submit(){
		
        $userid = Sentry::getUser()->id;
		$user = Sentry::getUser();
		$student = Student::findOrFail($user->id);
        $ssubjects = $student->extra;
        $subjects = unserialize($ssubjects);
        $truth = self::subjectValidator($user->id,$subjects,Input::get('subject'));
        if($truth == 0){
            return Redirect::to(URL::previous());
        }
		$messages = array(
			'title.required'=>'The :attribute is Required.',
			'related_tutorial.unique' =>'You have already submitted a Assessment for this tutorial. Please Update it.You can only submit a Assessment Per Tutorial',
			'required'=>'The :attribute is Required'
			);
		$validator = Validator::make(Input::all(),array(
			'id'=>'required',
			'title'=>'required|max:128|min:5',
			'description'=>'max:1024',
			'related_tutorial'=>"required|unique:assessments,tutorialid,NULL,id,studentid,".$userid,
			'submitted_to'=>'required',
			'subject'=>'required',
			'assessment_type'=>'required',
			'attachments'=>'mimes:pdf,PDF,jpg,JPG,jpeg,JPEG,png,PNG,bmp,BMP,gif,GIF,doc,docx,odt,ppt,pptx'
			),$messages);
		if($validator->fails()){
			Input::flash();
			return Redirect::to('/assessment/submit')->withErrors($validator);
		}
		$assessment = new Assessments;
		$assessment->title = Input::get('title');
		$assessment->description = Input::get('description');
		$assessment->assessmenttype = Input::get('assessment_type');
		$assessment->tutorialid = Input::get('related_tutorial');
		$assessment->teacherid = Input::get('submitted_to');
		$assessment->subjectid = Input::get('subject');
		$assessment->studentid = Sentry::getUser()->id;
		$assessment->save();
		$newassessment = DB::table('assessments')->orderby('id','desc')->first();


        if(Input::hasFile('attachments')){
        	$files =  Input::file('attachments');
        	foreach($files as $file){
        		if($file){
        		$name = $file->getClientOriginalName();
				$file->move(app_path().'/attachments/assessment-'.$newassessment->id.'/',$name);                    
        
        		}
        	}
    	
        }
		// Input::file('attachment1')->move(app_path().'/attachments/assessment-'.$assessment->id.'/',$name);  

		return View::make('site.assessment.update')->with('id',$newassessment->id)->nest('header','main.header');
	}
	public function submitview(){
		return View::make('site.assessment.new')->nest('header','main.header');
	}
	public function update($id){

		$userid = Sentry::getUser()->id;
		$messages = array(
			'title.required'=>'The :attribute is Required.',
			'required'=>'The :attribute is Required'
			);
		$assessment = Assessments::find($id);
		$validator = Validator::make(Input::all(),array(
			'id'=>'required',
			'title'=>'required|max:128|min:5',
			'description'=>'max:1024',
			'related_tutorial'=>"required|exists:assessments,tutorialid,studentid,".$userid,
			'submitted_to'=>'required',
			'subject'=>'required',
			'assessment_type'=>'required',
			'attachments'=>'mimes:pdf,PDF,jpg,JPG,jpeg,JPEG,png,PNG,bmp,BMP,gif,GIF,doc,docx,odt,ppt,pptx',
			),$messages);
		if($validator->fails()){
			Input::flash();
			return Redirect::to('/assessment/submit')->withErrors($validator);
		}
		$assessment = Assessments::find($id);
		$assessment->title = Input::get('title');
		$assessment->description = Input::get('description');
		$assessment->assessmenttype = Input::get('assessment_type');
		$assessment->tutorialid = Input::get('related_tutorial');
		$assessment->teacherid = Input::get('submitted_to');
		$assessment->subjectid = Input::get('subject');
		$assessment->studentid = Sentry::getUser()->id;
		$assessment->save();
		$newassessment = DB::table('assessments')->orderby('id','desc')->first();

        if(Input::hasFile('attachments')){
        	$files =  Input::file('attachments');
        	foreach($files as $file){
        		if($file){
        		$name = $file->getClientOriginalName();
				$file->move(app_path().'/attachments/assessment-'.$newassessment->id.'/',$name);                    
        
        		}
        	}
    	
        }
		// Input::file('attachment1')->move(app_path().'/attachments/assessment-'.$assessment->id.'/',$name);  

		return View::make('site.assessment.update')->with('id',$newassessment->id)->nest('header','main.header');
	}
	

	public function updateview($id){
		return View::make('site.assessment.update')->with('id',$id)->nest('header','main.header');
	}
	public function updatelist(){
		return View::make('site.assessments')->nest('header','main.header');
	}
	public function download($id,$file){
		$tutorial = Assessments::find($id);
		$studentid = $tutorial->studentid;
		if($studentid == Sentry::getUser()->id){
			return Response::download(app_path().'/attachments/assessment-'.$id.'/'.$file);
		}
		else
		{
			return "UNAUTHORISED DOWNLOAD";
		}
	}
	public function attachmentDelete($id,$file){
		$tutorial = Assessments::find($id);
		$studentid = $tutorial->studentid;
		if($studentid == Sentry::getUser()->id){
			File::delete(app_path().'/attachments/assessment-'.$id.'/'.$file);
			return Redirect::to(URL::previous());
		}
		else
		{
			return "UNAUTHORISED DELETE OPERATION";
		}
	}


	public function teacherUpdate($id){
		$assessmentid = $id;

		$assessment = Assessments::findOrFail($assessmentid);
		$userid = $assessment->studentid;
		$validator = Validator::make(Input::all(),
			[
			'id'=>'required|exists:assessments,id',
			'title'=>'required|max:128|min:5|exists:assessments,title,id,'.$id,
			'description'=>'max:1024|exists:assessments,description,id,'.$id,
			'related_tutorial'=>"required|exists:assessments,tutorialid,studentid,".$userid,
			'submitted_to'=>'required|exists:assessments,teacherid,id,'.$id,
			'subject'=>'required|exists:assessments,subjectid,id,'.$id,
			'assessment_type'=>'required|exists:assessments,assessmenttype,id,'.$id,
			'marks'=>'required|integer|between:0,20',
			'remarks'=>'required|max:1024|min:4'
			]);
		if($validator->fails()){
			return Redirect::to(URL::previous())->withErrors($validator);
		}
		$assessment->marks = Input::get('marks');
		$assessment->result = Input::get('remarks');
		$assessment->save();
		return Redirect::to(URL::previous());
	}

	private function subjectValidator($id,$subjects,$subject){
        foreach($subjects as $s){
            if($s == $subject){
                return 1;
            }
        }
        return 0;
    }
}