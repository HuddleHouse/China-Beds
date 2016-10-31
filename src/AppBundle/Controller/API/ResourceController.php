<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Resource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resource controller.
 *
 * @Route("/api")
 */
class ResourceController extends Controller
{
    /**
     * Creates a new resource entry.
     *
     * @Route("/api_save_resource", name="api_save_resource")
     * @Method({"GET", "POST"})
     */
    public function saveResourceAction(Request $request)
    {
        $data = $request->get('resource');
        $file = $request->files->get('resource')['file'];

        try {
            $resource = new Resource();
            $resource->setName($data['name']);
            $resource->setFile($file);
            $resource->upload();
            $resource->setDateCreated(new \DateTime());
            $this->getDoctrine()->getEntityManager()->persist($resource);
            $this->getDoctrine()->getEntityManager()->flush();

            $rtn = array(
                '<a href="/' . $resource->getWebPath() . '" target="_blank">' . $resource->getName() . '</a>',
                $resource->getDateCreated()->format('m/d/y H:i:s'),
                '<a download class="btn btn-raised btn-xs" href="/' . $resource->getWebPath() . '">Download</a><button class="btn btn-raised btn-xs" onclick="deleteResource('. $resource->getId() .', this)">Delete</button>'
            );

            return new JsonResponse(array(true, $rtn));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }

    /**
     * Show a Resource Upload Form
     *
     * @Route("/api_show_resource", name="api_show_resource")
     * @Method("GET")
     */
    public function showAction()
    {
        try {
            $template = $this->renderView('@App/Resource/modal-show.html.twig');
            return new JsonResponse(array(true, $template));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }

    /**
     * Deletes a Resource file and entity.
     *
     * @Route("/api_resource_delete", name="api_resource_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $resource = $em->getRepository('AppBundle:Resource')->find($request->get('resource_id'));
            $em->remove($resource);
            $em->flush();

            return new JsonResponse(array(true));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }
}
