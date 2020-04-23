<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("project/{id}/task/add", name="add_task")
     */
    public function addTast(Project $project,Request $request)
    {
        $task = new Task;
        $taskForm =$this->createForm(TaskType::class,$task);
        return $this->render('task/add.html.twig', [
            'project' => $project,
            'taskForm' => $taskForm->createView()
        ]);
    }
}
