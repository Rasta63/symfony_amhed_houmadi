<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/projects", name="show_projects",methods={"GET"})
     */
    public function showProjects(ProjectRepository $projectRepository,SerializerInterface $serializer)
    {
        $projects = $projectRepository->findAll();
        $serializerProjects = $serializer->serialize($projects,'json',['groups'=>['projects']]);
        return new JsonResponse($serializerProjects,200,[],true);
    }

    /** 
     * @Route("/api/projects/{id}", name="show_detail_project",methods={"GET"})
     */
    public function showDetailsProject($id,ProjectRepository $projectRepository,SerializerInterface $serializer)
    {
        $project = $projectRepository->find($id);
        if (! $project instanceOf Project) {
            return $this->json([
                                'Status'=>400,
                                'message' => 'Not found'
                                ],
                                400
                            );
        }
        $serializerProjects = $serializer->serialize($project,'json',['groups'=>['details']]);
        return new JsonResponse($serializerProjects,200,[],true);
    }
}
