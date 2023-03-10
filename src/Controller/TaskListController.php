<?php

namespace App\Controller;

use App\Entity\TaskList;
use App\Entity\Board;
use App\Repository\BoardRepository;
use App\Form\TaskListType;
use App\Repository\TaskListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/taskList')]
class TaskListController extends AbstractController
{
    #[Route('/', name: 'app_task_list_index', methods: ['GET'])]
    public function index(TaskListRepository $taskListRepository): Response
    {
        return $this->render('task_list/index.html.twig', [
            'task_lists' => $taskListRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_task_list_new', methods: ['GET', 'POST'])]
    public function new($id, Request $request, TaskListRepository $taskListRepository): Response
    {
        $taskList = new TaskList();
        $taskList->setBoardId($id);
        $form = $this->createForm(TaskListType::class, $taskList);
        $userLogged = $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskListRepository->save($taskList, true);

            return $this->redirectToRoute('app_board_show', ["id" => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_list/new.html.twig', [
            'task_list' => $taskList,
            'form' => $form,
            'userLogged' => $userLogged
        ]);
    }

    #[Route('/{id}', name: 'app_task_list_show', methods: ['GET'])]
    public function show(TaskList $taskList): Response
    {
        $userLogged = $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->render('task_list/show.html.twig', [
            'task_list' => $taskList,
            'userLogged' => $userLogged
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaskList $taskList, TaskListRepository $taskListRepository): Response
    {
        $form = $this->createForm(TaskListType::class, $taskList);
        $form->handleRequest($request);
        $userLogged = $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if ($form->isSubmitted() && $form->isValid()) {
            $taskListRepository->save($taskList, true);

            return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_list/edit.html.twig', [
            'task_list' => $taskList,
            'form' => $form,
            'userLogged' => $userLogged
        ]);
    }

    #[Route('/{id}/{board_id}', name: 'app_task_list_delete', methods: ['GET', 'POST'])]
    public function delete($id, $board_id, Request $request, TaskList $taskList, TaskListRepository $taskListRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskList->getId(), $request->request->get('_token'))) {
            $taskListRepository->remove($taskList, true);
        }
        return $this->redirectToRoute('app_board_show', ["id" => $board_id], Response::HTTP_SEE_OTHER);
    }
}
