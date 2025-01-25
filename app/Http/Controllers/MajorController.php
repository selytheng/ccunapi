<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class MajorController extends Controller
{
    public function create(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name'          => ['required', 'string', Rule::unique('majors')],
                'partner_id'   => 'required|string',
                'description' => ['required', 'string'],
                'logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $validator->validated();
            if ($req->hasFile('logo')) {
                $logo = $this->storeImage($req->file('logo'), 'uploads/majors');
                $data['logo'] = 'storage/' . $logo;
            }

            $major = Major::create($data);

            return response()->json($major, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function get()
    {
        $majors = Major::all();
        return response()->json($majors, Response::HTTP_OK);
    }

    public function getAllCoursesInMajor($id)
    {
        try {
            $major = Major::find($id);
            if (!$major) {
                return response()->json(['message' => 'Major not found.'], Response::HTTP_NOT_FOUND);
            }
            $courses = $major->courses;
            return response()->json($courses, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function getById($id)
    {
        $majors = Major::find($id);
        if (!$majors) {
            return response()->json(['message' => 'Major not found.'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($majors, Response::HTTP_OK);
    }

    public function update(Request $req, $id)
    {
        try {
            $validator = $req->validate([
                'name'          => ['required', 'string', Rule::unique('majors')],
                'description' => ['nullable', 'string'],
                'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            $updateMajor = Major::find($id);

            if (!$updateMajor) {
                return response()->json(['message' => 'Major not found.'], Response::HTTP_NOT_FOUND);
            }

            if ($req->hasFile('logo')) {
                $logoPath = $req->file('logo')->store('uploads/majors', 'public');
                $validatedData['logo'] = $logoPath;
            }

            $updateMajor->update($validator);
            return response()->json($updateMajor, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function delete($id)
    {
        try {
            $deleteMajor = Major::find($id);

            if (!$deleteMajor) {
                return response()->json(['message' => 'Major not found.'], Response::HTTP_NOT_FOUND);
            }

            $deleteMajor->delete();
            return response()->json(['message' => 'Major deleted successfull.'], Response::HTTP_NO_CONTENT);
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
        return response()->json(
            [
                'error' => 'An unexpected error occurred.'
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
