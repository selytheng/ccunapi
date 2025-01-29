<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\FileUploadController;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    public function create(Request $req)
    {
        try {
            $validator = $req->validate([
                'title' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'partner_id' => 'required|integer|exists:partners,id',
                'co_host' => 'nullable|array',
                'sponsor' => 'nullable|array',
                'location' => 'required|string',
                'status' => 'required|in:active,inactive',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            // Handle main image upload
            $image = FileUploadController::storeImage($req->file('image'), 'uploads/trainings');
            $validator['image'] = $image;

            // Handle gallery images upload
            $gallery = [];
            if ($req->hasFile('gallery')) {
                foreach ($req->file('gallery') as $file) {
                    $galleryImage = FileUploadController::storeImage($file, 'uploads/trainings/gallery');
                    $gallery[] = $galleryImage;
                }
            }
            $validator['gallery'] = $gallery;

            $training = Training::create(array_merge($validator, [
                'created_at' => Carbon::now('Asia/Phnom_Penh'),
                'updated_at' => Carbon::now('Asia/Phnom_Penh'),
            ]));

            return response()->json($training, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function get(Request $req)
    {
        $title = $req->input('title');
        if ($title) {
            $trainings = Training::where('title', 'like', '%' . $title . '%')->get();
        } else {
            $trainings = Training::all();
        }
        return response()->json($trainings, Response::HTTP_OK);
    }

    public function getById($id)
    {
        $training = Training::with('partner')->find($id);
        if (!$training) {
            return response()->json(['message' => 'Training not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($training, Response::HTTP_OK);
    }

    public function update(Request $req, $id)
    {
        try {
            $validator = $req->validate([
                'title'         => 'required|string',
                'partner_id'    => 'integer|exists:partners,id',
                'co_host'       => 'nullable|array',
                'sponsor'       => 'nullable|array',
                'image'         => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description'   => 'nullable|string',  // Changed to nullable
                'location'      => 'nullable|string',
                'status'        => 'nullable|string',
                'start_date'    => 'nullable|date',
                'end_date'      => 'nullable|date|after:start_date',
            ]);

            $updateTraining = Training::find($id);
            if (!$updateTraining) {
                return response()->json(['message' => 'Training not found'], Response::HTTP_NOT_FOUND);
            }

            // Handle image upload if present
            if ($req->hasFile('image')) {
                $image = FileUploadController::storeImage($req->file('image'), 'uploads/trainings');
                $validator['image'] = $image; // Add the image path to the data
            }

            // Update the course with validated data and handle the update process
            $updateTraining->update(array_merge($validator, [
                'updated_at' => Carbon::now('Asia/Phnom_Penh'),
            ]));

            return response()->json($updateTraining, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function removeGalleryImages(Request $req, $id)
    {
        try {
            $validator = $req->validate([
                'removegalleryindex' => 'nullable|array',
                'removegalleryindex.*' => 'integer',
            ]);

            $training = Training::find($id);
            if (!$training) {
                return response()->json(['message' => 'Training not found'], Response::HTTP_NOT_FOUND);
            }

            if (!empty($validator['removegalleryindex'])) {
                $gallery = $training->gallery;

                foreach ($validator['removegalleryindex'] as $indexToRemove) {
                    if (isset($gallery[$indexToRemove])) {
                        unset($gallery[$indexToRemove]);
                    } else {
                        return response()->json(['message' => 'Invalid gallery index: ' . $indexToRemove], Response::HTTP_BAD_REQUEST);
                    }
                }

                // Reindex the array after removal
                $training->gallery = array_values($gallery);
                $training->updated_at = Carbon::now('Asia/Phnom_Penh');
                $training->save();

                return response()->json($training, Response::HTTP_OK);
            }

            return response()->json(['message' => 'No images to remove'], Response::HTTP_BAD_REQUEST);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    public function addGalleryImages(Request $req, $id)
    {
        try {
            $validator = $req->validate([
                'addgalleries.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $training = Training::find($id);
            if (!$training) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => [
                        'training' => ['Training not found.']
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

            if ($req->hasFile('addgalleries')) {
                $gallery = $training->gallery ?? [];
                foreach ($req->file('addgalleries') as $file) {
                    try {
                        $newImage = FileUploadController::storeImage($file, 'uploads/trainings/gallery');
                        $gallery[] = $newImage;
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'Validation Error',
                            'errors' => [
                                'image' => ['Error uploading image: ' . $e->getMessage()]
                            ]
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
                $training->gallery = $gallery;
                $training->updated_at = Carbon::now('Asia/Phnom_Penh');
                $training->save();

                return response()->json($training, Response::HTTP_OK);
            }

            return response()->json([
                'message' => 'Validation Error',
                'errors' => [
                    'images' => ['No images to add.']
                ]
            ], Response::HTTP_BAD_REQUEST);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => [
                    'server' => ['An unexpected error occurred.']
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function delete($id)
    {
        try {
            $training = Training::find($id);
            if (!$training) {
                return response()->json(['message' => 'Training not found'], Response::HTTP_NOT_FOUND);
            }
            $training->delete();
            return response()->json(['message' => 'Training deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    protected function handleValidationException(ValidationException $e)
    {
        return response()->json(
            [
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    protected function handleUnexpectedException(\Exception $e)
    {
        Log::error('Unexpected error occurred', ['exception' => $e]);
        return response()->json(
            [
                'error' => 'An unexpected error occurred.',
                'details' => $e->getMessage(), // Add the exception message to the response
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
