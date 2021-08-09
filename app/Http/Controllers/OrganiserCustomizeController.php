<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use File;
use Image;
use Illuminate\Http\Request;
use Validator;

class OrganiserCustomizeController extends MyBaseController
{
    /**
     * Show organiser setting page
     *
     * @param $organiser_id
     * @return mixed
     */
    public function showCustomize($organiser_id)
    {
        $data = [
            'organiser' => Organiser::scope()->findOrFail($organiser_id),
        ];

        return view('ManageOrganiser.Customize', $data);
    }

    /**
     * Edits organiser settings / design etc.
     *
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function postEditOrganiser(Request $request, $organiser_id)
    {
        $organiser = Organiser::scope()->find($organiser_id);

        $chargeTax = $request->get('charge_tax');
        if ($chargeTax == 1) {
            $organiser->addExtraValidationRules();
        }

        if (!$organiser->validate($request->all())) {
            return response()->json([
                'status'   => 'error',
                'messages' => $organiser->errors(),
            ]);
        }

        $organiser->name = $request->get('name');
        $organiser->about = prepare_markdown($request->get('about'));
        $organiser->google_analytics_code = $request->get('google_analytics_code');
        $organiser->google_tag_manager_code = $request->get('google_tag_manager_code');
        $organiser->email = $request->get('email');
        $organiser->enable_organiser_page = $request->get('enable_organiser_page');
        $organiser->facebook = $request->get('facebook');
        $organiser->twitter = $request->get('twitter');

        $organiser->tax_name = $request->get('tax_name');
        $organiser->tax_value = round($request->get('tax_value'), 2);
        $organiser->tax_id = $request->get('tax_id');
        $organiser->charge_tax = ($request->get('charge_tax') == 1) ? 1 : 0;

        if ($request->get('remove_current_image') == '1') {
            $organiser->logo_path = '';
        }

        if ($request->hasFile('organiser_logo')) {
            $organiser->setLogo($request->file('organiser_logo'));
        }

        $organiser->save();

        session()->flash('message', trans("Controllers.successfully_updated_organiser"));

        return response()->json([
            'status'      => 'success',
            'redirectUrl' => '',
        ]);
    }

    /**
     * Edits organiser profile page colors / design
     *
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function postEditOrganiserPageDesign(Request $request, $organiser_id)
    {
        $organiser = Organiser::scope()->findOrFail($organiser_id);

        $rules = [
            'page_bg_color'        => ['required'],
            'page_header_bg_color' => ['required'],
            'page_text_color'      => ['required'],
        ];
        $messages = [
            'page_header_bg_color.required' => trans("Controllers.error.page_header_bg_color.required"),
            'page_bg_color.required'        => trans("Controllers.error.page_bg_color.required"),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $organiser->page_bg_color        = $request->get('page_bg_color');
        $organiser->page_header_bg_color = $request->get('page_header_bg_color');
        $organiser->page_text_color      = $request->get('page_text_color');

        $organiser->save();

        return response()->json([
            'status'  => 'success',
            'message' => trans("Controllers.organiser_design_successfully_updated"),
        ]);
    }
}
