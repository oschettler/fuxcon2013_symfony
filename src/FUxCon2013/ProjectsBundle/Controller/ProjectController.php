<?php

namespace FUxCon2013\ProjectsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FUxCon2013\ProjectsBundle\Entity\Project;
use FUxCon2013\ProjectsBundle\Form\ProjectType;

/**
 * Project controller.
 */
class ProjectController extends Controller
{
    const NO_COL = 3;
    const PAGE_SIZE = 5;

    /**
     * Send flash message
     *
     * @param $message
     * @param string $type
     */
    private function flash($message, $type = 'error')
    {
        $this->get('session')->getFlashBag()->add($type, $message);
    }

    /**
     * Redirect to the project detail page
     *
     * @param $project
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function show($project)
    {
        return $this->redirect(
            $this->generateUrl('project_show', array('id' => $project->getId()))
        );
    }

    /**
     * @Route("/", defaults={"offset" = 1, "tag" = null})
     * @Route("/page:{offset}", name="project_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, $offset = 1, $tag = null)
    {
        $repo = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('FUxCon2013ProjectsBundle:Project');

        $limit = 10;
        $from  = (($offset * $limit) - $limit);

        $totalCount = $repo->count();
        $totalPages = ceil($totalCount / $limit);

        $projects = $repo->findPaginated($from, $limit);

        $columns = array();
        foreach ($projects as $i => $project) {
            $col = $i % self::NO_COL;
            $columns[$col][] = $project;
        }

        $vars = array(
            'columns' => $columns,
            'width' => 12 / self::NO_COL,
            'page' => $offset,
            'totalPages' => $totalPages,
            'body_class' => 'projects-index',
        );

        return $vars;
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/", name="project_create")
     * @Method("POST")
     * @Template("FUxCon2013ProjectsBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $project = new Project();
        $form = $this->createForm(new ProjectType(), $project);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $project->setUser($this->getUser());

            $em->persist($project);
            $em->flush();

            $this->get('fpn_tag.tag_manager')->saveTagging($project);


            // Picture processed after saving. We need the project ID

            if ($project->getPicture() && $project->processPicture()) {
                $this->flash('The project has been saved', 'success');
            }
            else {
                $this->flash('Picture could not be saved. Please try again.');
            }
            return $this->show($project);
        }
        else {
            $this->flash('The project could not be saved. Please, try again.');
        }

        return array(
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/project/new", name="project_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $form   = $this->createForm(new ProjectType(), new Project());

        return array(
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/project/{id}", name="project_show")
     * @Method("GET")
     * @Template()
     *
     * Uses type hint "Project $project" to implicitely invoke ParamConverter
     */
    public function showAction(Project $project)
    {
        $this->get('fpn_tag.tag_manager')->loadTagging($project);

        $project->dates = '';

        $startDate = $project->getStartDate()->format('m/Y');

        if ('11/-0001' != $startDate) {
            $project->dates = $startDate;
        }
        $endDate = $project->getEndDate()->format('m/Y');
        if ('11/-0001' != $endDate) {
            if (!empty($project->dates)) {
                $project->dates .= ' ';
            }
            $project->dates .= 'until ' . $endDate;
        }
        else
            if (!empty($project->dates)) {
                $project->dates = 'since ' . $project->dates;
            }

        return array(
            'project' => $project
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/project/{id}/edit", name="project_edit")
     * @Method("GET")
     * @Template()
     *
     * Uses type hint "Project $project" to implicitely invoke ParamConverter
     */
    public function editAction(Project $project)
    {
        if (!$this->get('security.context')->isGranted('MAY_EDIT', $project)) {
            $this->flash('You are not allowed to edit this project');
            return $this->show($project);
        }

        $this->get('fpn_tag.tag_manager')->loadTagging($project);

        $editForm = $this->createForm(new ProjectType(), $project);

        return array(
            'project'      => $project,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/project/{id}", name="project_update")
     * @Method("PUT")
     * @Template("FUxCon2013ProjectsBundle:Project:edit.html.twig")
     *
     * Uses type hint "Project $project" to implicitely invoke ParamConverter
     */
    public function updateAction(Request $request, Project $project)
    {
        if (!$this->get('security.context')->isGranted('MAY_EDIT', $project)) {
            $this->flash('You are not allowed to edit this project');
            return $this->show($project);
        }

        $editForm = $this->createForm(new ProjectType(), $project);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            if (!$project->getPicture() || $project->processPicture()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();

                $this->get('fpn_tag.tag_manager')->saveTagging($project);

                $this->flash('The project has been saved', 'success');
                return $this->show($project);
            }
            else {
                $this->flash('Picture could not be saved. Please try again.');
            }
        }
        else {
            $this->flash('The project could not be saved. Please, try again.');
        }

        return array(
            'project'      => $project,
            'edit_form'   => $editForm->createView(),
        );
    }
}
