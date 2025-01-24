<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Storage;
use App\Http\Controllers\PartnerContactController;

class PartnerController extends Controller
{
    public function create(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name'        => ['required', 'string', Rule::unique('partners')],
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
                $logo = $this->storeImage($req->file('logo'), 'uploads/partners');
                $data['logo'] = 'storage/' . $logo;
            }

            $partner = Partner::create($data);

            // Call PartnerContactController to create empty contact
            app(PartnerContactController::class)->createContact($partner->id);

            return response()->json($partner, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }



    public function get()
    {
        $partners = Partner::all();
        return response()->json($partners, Response::HTTP_OK);
    }

    public function getAllMajorInPartner($id)
    {
        try {
            $partner = Partner::find($id);
            if (!$partner) {
                return response()->json(['message' => 'Partner not found.'], Response::HTTP_NOT_FOUND);
            }
            $majors = $partner->majors;
            return response()->json($majors, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function getAllCoursesInPartner($id)
    {
        try {
            $partner = Partner::find($id);
            if (!$partner) {
                return response()->json(['message' => 'Partner not found.'], Response::HTTP_NOT_FOUND);
            }

            // Retrieve all courses through the partner's majors
            $courses = $partner->majors->flatMap(function ($major) {
                return $major->courses;
            });

            return response()->json($courses, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function getAllEventsInPartner($id)
    {
        try {
            $partner = Partner::find($id);
            if (!$partner) {
                return response()->json(['message' => 'Partner not found.'], Response::HTTP_NOT_FOUND);
            }

            // Retrieve all events related to the partner
            $events = $partner->events->map(function ($event) {
                return [
                    'id'          => $event->id,
                    'title'       => $event->title,
                    'description' => $event->description,
                    'image'       => $event->image,
                ];
            });

            return response()->json($events, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }


    public function getById($id)
    {
        $partners = Partner::find($id);
        if (!$partners) {
            return response()->json(['message' => 'Partner not found.'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($partners, Response::HTTP_OK);
    }

    public function update(Request $req, $id)
    {
        try {
            $validatedData = $req->validate([
                'name'          => ['required', 'string', Rule::unique('partners')],
                'description' => ['nullable', 'string'],
                'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            $updatePartner = Partner::find($id);

            if (!$updatePartner) {
                return response()->json(['message' => 'Partner not found.'], Response::HTTP_NOT_FOUND);
            }


            if ($req->hasFile('logo')) {
                $logoPath = $req->file('logo')->store('uploads/partners', 'public');
                $validatedData['logo'] = $logoPath;
            }

            $updatePartner->update($validatedData);

            return response()->json($updatePartner, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }



    public function delete($id)
    {
        try {
            $deletePartner = Partner::find($id);

            if (!$deletePartner) {
                return response()->json(['message' => 'Partner not found.'], Response::HTTP_NOT_FOUND);
            }

            $deletePartner->delete();
            return response()->json(['message' => 'Partner deleted successfull.'], Response::HTTP_NO_CONTENT);
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
        \Log::error('Unexpected error occurred: ' . $e->getMessage());
        return response()->json(
            [
                'error' => 'An unexpected error occurred...'
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    protected function storeImage($file, $folder)
    {
        $path = $file->store($folder, 'public');
        return $path;
    }
}
