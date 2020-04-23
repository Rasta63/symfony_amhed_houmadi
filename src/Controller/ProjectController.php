<?php

namespace App\Controller;


use App\Entity\Project;
use App\Entity\Task;
use App\Form\ChoiceStatusType;
use App\Form\ProjectType;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    
    
     
    /**
     * @Route("/projects", name="project_list")
     */
    public function swhoProjects(Request $request,ProjectRepository $projectRepository)
    {
        
        $project= new Project;
        $projectForm = $this->createForm(ProjectType::class,$project);
        $projectForm->handleRequest($request);
        if ($projectForm->isSubmitted() && $projectForm->isValid()) {
            $project->setStartedAt(new \DateTime());
            $project->setStatus('Nouveau');
            $this->getDoctrine()->getManager()->persist($project);
            $this->getDoctrine()->getManager()->flush();  
        }
        $projects = $projectRepository->findAll();
        return $this->render('project/list.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * @Route("/projects/add", name="add_project")
     */
    public function addProject(Request $request){
        $project= new Project;
        $projectForm = $this->createForm(ProjectType::class,$project);

        return $this->render('project/add.html.twig', [
            'projectForm' => $projectForm->createView()
         ]);
    }
    /**
     * @Route("/project/{id}", name="project_detail")
     */
    public function detailProject(Project $project, Request $request, TaskRepository $taskRepository){

        //recuperer et modiffier le statut d'un projet
        $statusForm = $this->createForm(ChoiceStatusType::class);
        $statusForm->handleRequest($request);
        if ($statusForm->isSubmitted() && $statusForm->isValid() && $project->getStatus()!=='Terminé' ) {
            $status = $statusForm->getData()['Statut'];
            switch ($status) {
                case 0:
                    $project->setStatus('Nouveau');
                    break;
                case 1:
                    $project->setStatus('En cours');
                    break;
                case 2:
                    $project->setStatus('Terminé');
                    $project->setEndedAt(new \DateTime());
                    break;
            }

            $this->getDoctrine()->getManager()->persist($project);
            $this->getDoctrine()->getManager()->flush();  
        }

        //ajout d'une tache envoyer par le formulaire de task
        $task= new Task;
        $taskForm = $this->createForm(TaskType::class,$task);
        $taskForm->handleRequest($request);
        if ($taskForm->isSubmitted() && $taskForm->isValid() && $project->getStatus()!=='Terminé') {
            $task->setProject($project);
            $task->setCreatedAt(new \DateTime());

            $this->getDoctrine()->getManager()->persist($task);
            $this->getDoctrine()->getManager()->flush();  
        }
        $tasks = $taskRepository->findBy(['project'=>$project]);
        
        return $this->render('project/detail.html.twig', [
            'project' => $project,
            'statusForm' => $statusForm->createView(),
            'tasks' => $tasks
         ]);
    }
}
