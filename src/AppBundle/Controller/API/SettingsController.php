<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Setting controller.
 *
 * @Route("/api")
 */
class SettingsController extends Controller
{
    /**
     * Creates a new ledger entry.
     *
     * @Route("/api_save_settings", name="api_save_settings")
     * @Method({"GET", "POST"})
     */
    public function saveSettingsAction(Request $request)
    {
        $settings_service = $this->get('settings_service');
        $data = $request->get('data');

        try {
            foreach ($data as $setting)
                $settings_service->set($setting['name'], $setting['value']);
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }

        return new JsonResponse(array(true));
    }
}
