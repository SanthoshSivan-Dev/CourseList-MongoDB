<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Course;
use App\Models\Capability;
use App\Models\Skill;

class CourseController extends Controller
{
    public function store(Request $request){
        
        $input = $request->all();
        $validator = Validator::make($input, [
            'courseName' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'courseImage' => 'required',
            'capability.*.capabilityName' => 'required',
            'capability.*.skill.*.skillName' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // save course
        $course = new Course();
        $course->courseName = $input['courseName'];
        $course->startDate = $input['startDate'];
        $course->endDate = $input['endDate'];
        $course->courseImage = $input['courseImage'];
        $course->save();

        // save capability
        foreach ($input['capability'] as $capabilityData) {
            $capability = new Capability();
            $capability->capabilityName = $capabilityData['capabilityName'];
            $capability->course()->associate($course);
            $capability->save();

            // save skill
            foreach ($capabilityData['skill'] as $skillData) {
                $skill = new Skill();
                $skill->skillName = $skillData['skillName'];
                $skill->capability()->associate($capability);
                $skill->save();
            }
        }

        return response()->json(['data' => 'Created Successfully']);
    }

    public function getCourseListapi(Request $request){

        $input = $request->all();

        $page = isset($input['page']) ? $input['page'] : 1;
        $limit = isset($input['limit']) ? $input['limit'] : 10;

        // getting list 
        $courses = Course::with('capabilities.skills')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        // get total count 
        $totalCourses = Course::count();

        $response = [
            'statuscode' => 200,
            'data' => $courses,
            'pageable' => [
                'total' => $totalCourses,
                'limit' => $limit,
                'page' => $page,
            ],
        ];

        return response()->json($response);
    }

    public function updateCourse(Request $request, $courseId){
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        // update the course list
        $course->update($request->only(['courseName', 'startDate', 'endDate', 'courseImage']));


        // checking capability
        if ($request->has('capability')) {
            foreach ($request->input('capability') as $capabilityData) {
                $capability = Capability::find($capabilityData['capabilityId']);
    
                if ($capability) {
                    if (isset($capabilityData['capabilityId'])) {
                        $capability->update(['capabilityName' => $capabilityData['capabilityName']]);
                    }
    
                    // checking skill
                    if(isset($capabilityData['skill'])){
                        foreach ($capabilityData['skill'] as $skillData) {
                            $skill = Skill::find($skillData['skillId']);
        
                            if ($skill) {
                                if (isset($capabilityData['capabilityId']) && isset($skillData['skillId'])) {
                                    $skill->update(['skillName' => $skillData['skillName']]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['data' => 'Updated Successfully']);
    }

    public function deleteCourse(Request $request){
        $input = $request->all();
        $courseId = $input['courseId'];
        
        Course::where('id', $courseId)->delete();
        Capability::where('course_id', $courseId)->delete();
        Skill::where('course_id', $courseId)->delete();

        return response()->json(['data' => 'Deleted']);
    }
}