<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;

class OrganiserController extends MyBaseController
{
    /**
     * Show the select organiser page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showSelectOrganiser()
    {
        return view('ManageOrganiser.SelectOrganiser');
    }

    /**
     * Show the create organiser page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showCreateOrganiser()
    {
        return view('ManageOrganiser.CreateOrganiser');
    }

    /**
     * Create the organiser
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function postCreateOrganiser(Request $request)
    {
        $organiser = Organiser::createNew(false, false, true);

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
        $organiser->email = $request->get('email');
        $organiser->facebook = $request->get('facebook');
        $organiser->twitter = $request->get('twitter');
        $organiser->confirmation_key = Str::random(15);

        $organiser->tax_name = $request->get('tax_name');
        $organiser->tax_value = round($request->get('tax_value'),2);
        $organiser->tax_id = $request->get('tax_id');
        $organiser->charge_tax = ($chargeTax == 1) ? 1 : 0;

        if ($request->hasFile('organiser_logo')) {
            $organiser->setLogo($request->file('organiser_logo'));
        }

        $organiser->save();

        // After the organiser gets added as the first run, we should also update the super user to
        // belong to that organiser
        if ($request->get('first_run') === 'yup') {
            User::first()->update(['organiser_id' => $organiser->id]);
        }

        session()->flash('message', trans("Controllers.successfully_created_organiser"));

        return response()->json([
            'status'      => 'success',
            'message'     => trans("Controllers.refreshing"),
            'redirectUrl' => route('showOrganiserEvents', [
                'organiser_id' => $organiser->id,
                'first_run'    => 1
            ]),
        ]);
    }
}
