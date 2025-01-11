<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\FileUploadController;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function create(Request $req)
    {
        try {
            $validator = $req->validate([
                'name'          => 'required|string',
                'major_id'   => 'required|integer|exists:majors,id',
                'year_id'   => 'required|integer|exists:years,id',
                'image'         => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description'     => 'required|string',
                'link'     => 'required|string',
            ]);

            // Handle image upload
            $image = FileUploadController::storeImage($req->file('image'), 'uploads/courses');
            $validator['image'] = $image;

            // Create product with timezone conversion
            $course = Course::create(array_merge($validator, [
                'created_at' => Carbon::now('Asia/Phnom_Penh'),
                'updated_at' => Carbon::now('Asia/Phnom_Penh'),
            ]));

            return response()->json($course, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function get(Request $req)
    {
        // validate name as an input to search the product
        $name = $req->input('name');
        if ($name) {    // search product
            $products = Course::where('name', 'like', '%' . $name . '%')->get();
            return response()->json($products, Response::HTTP_OK);
        } else {        //get all product
            $products = Course::all();
            return response()->json($products, Response::HTTP_OK);
        }
    }
    public function getAllCourses()
    {
        try {
            // Fetch all courses with their associated partner_id
            $courses = Course::with('major.partner')->get()->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'description' => $course->description,
                    'image' => $course->image,
                    'link' => $course->link,
                    'major_id' => $course->major_id,
                    'year_id' => $course->year_id,
                    'partner_id' => $course->major->partner->id ?? null, // Use null if no partner is found
                ];
            });

            return response()->json($courses, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }


    public function getById($id)
    {
        $produtcs = Course::find($id);
        return response()->json($produtcs, Response::HTTP_OK);
    }

    public function update(Request $req, $id)
    {
        try {
            $validator = $req->validate([
                'name'          => 'required|string',
                'major_id'   => 'integer|exists:majors,id',
                'year_id'   => 'integer|exists:years,id',
                'image'         => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description'     => 'text',
                'link'     => 'string',
            ]);

            $updateCourse = Course::find($id);
            if (!$updateCourse) {
                return response()->json(['message' => 'Course not found'], Response::HTTP_NOT_FOUND);
            }

            // Handle image upload if present
            if ($req->hasFile('image')) {
                $image = FileUploadController::storeImage($req->file('image'), 'uploads/courses');
                $validator['image'] = $image;
            }

            // Update product with timezone conversion
            $updateCourse->update(array_merge($validator, [
                'updated_at' => Carbon::now('Asia/Phnom_Penh'),
            ]));

            return response()->json($updateCourse, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }


    public function delete($id)
    {
        try {
            $deleteCourse = Course::find($id);
            if (!$deleteCourse) {
                return response()->json(['message' => 'Course not found'], Response::HTTP_NOT_FOUND);
            }
            $deleteCourse->delete();
            return response()->json(['message' => 'Course deleted successfull.'], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    protected function handleValidationException(ValidationException $e)
    {
        return response()->json(
            [
                'message'   => 'Validation Error',
                'errors'    => $e->errors()
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    protected function handleUnexpectedException(\Exception $e)
    {
        Log::error('Unexpected error occurred', ['exception' => $e]);

        return response()->json(
            [
                'error' => 'An unexpected error occurred.'
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
