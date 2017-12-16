<?php

namespace AppBundle\Controller;


use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Usuario;
use AppBundle\Form\UsuarioType;
use Symfony\Component\HttpFoundation\JsonResponse;

    //----- Ruta General------//
    /**
     * @Route("/usuario")
     */

class UsuarioController extends Controller

{
    //++++++++++++++++++++++++++++++++++++++++++//
    /////////////////--VIEWS--////////////////////
    //++++++++++++++++++++++++++++++++++++++++++//


    //------Ruta del index del template usuario-----------//
    /**
     * @Route("/", name="usuario", options={"expose" = true})
     * @Method("GET")
     * @return JsonResponse
     */
    public function indexAction()
    {
        // replace this example code with whatever you need

        $datosUsuarioBD = $this->getDoctrine()->getRepository('AppBundle:Usuario');

        $usuarioList = $datosUsuarioBD->findAll();
        return $this->render('AppBundle:Usuario:usuario.html.twig',array("usuarioList"=>$usuarioList));
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * @Route("/new", name="nuevoUsuario", options={"expose" = true})
     * @Method("GET")
     * @return JsonResponse
     */
    public function newAction()
    {

        return $this->render('AppBundle:Usuario:nuevousuario.html.twig');
    }


    //-------------------------------------------------------------------------------------------------------------

    /**
     * @Route("/{id}",
     *     name="editUsuario",
     *     options={"expose" = true},
     *     requirements={"id"="\d+"})
     * @Method("GET")
     * @param Request $request
     * @param Usuario $usuario
     * @return JsonResponse
     */
    public function editAction(Request $request, Usuario $usuario)
    {
        $data = json_decode($this->get('serializer')->serialize($usuario, 'json'), true);

        return $this->render('AppBundle:Usuario:editusuario.html.twig', array("usuario"=>$data));
    }



    //++++++++++++++++++++++++++++++++++++++++++//
    /////////////////--APIs--/////////////////////
    //++++++++++++++++++++++++++++++++++++++++++//



    //--------- Guardar datos del nuevo usuario --------------//
    /**
     * @Route("/new/", name="createUsuario", options={"expose" = true})
     * @Method("POST")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postAction(Request $request){

        //obtener datos json del objeto request //
        $data = json_decode($request->getContent(), true);

        //crear un objeto de la clase Entity:Usuario//
        $usuario = new Usuario();

        //crear objeto de la clase Form:UsuarioType//
        $form = $this->createForm(UsuarioType::Class, $usuario);

        //finaliza y envia datos despues del debugeo interno de symfony
        $form->submit($data);


        //le cambio el formato al date time para que retorne un string y despues insertarlo
        $date = new \DateTime();
        $date = $date->format("y-M-d H:M a");



        //inserta dato extra fuera de la validacion de symfony///
        $usuario->setFechaRegistro($date);

        //preguntamos si es valido el formulario con el proceso de validacion de symfony
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            //dump("is Valid");

        }else{
            foreach ($form->getErrors() as $error)
            {
                $error[] = $error->getMessage();
            }
        }

        $newUsuario = json_decode($this->get('serializer')->serialize($usuario,'json'), true);

        return new JsonResponse($newUsuario);

    }


    //----------- Actualizar datos el Usuario----------------------//-

    /**
     * @Route("/{id}/",
     *     name="updUsuario",
     *     requirements={"id"="\d+"},
     *     options={"expose"=true})
     * @Method("PUT")
     * @param Request $request
     * @param Usuario $updusuario
     * @return JsonResponse
     */
    public function updAction(Request $request, Usuario $updusuario)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(UsuarioType::class, $updusuario);

        //le cambio el formato al date time para que retorne un string y despues insertarlo
        $date = new \DateTime();
        $date = $date->format("y-M-d H:m a");


        //inserta dato extra fuera de la validacion de symfony///
        $updusuario->setFechaRegistro($date);

        $form->submit($data);

        if ($form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }else{

            foreach ($form->getErrors() as $error){
                $errors[] = $error->getMessage();
            }
        }

        $updusuario = json_decode($this->get('serializer')->serialize($updusuario, 'json'), true);

        return new JsonResponse($updusuario);
    }

    /**
     * @Route("/{id}//",
     *     name="delUsuario",
     *     requirements={"id"="\d+"},
     *     options={"expose"=true})
     * @param Request $request
     * @param Usuario $delusario
     * @return JsonResponse
     */
    public function delAction(Request $request, Usuario $delusario){

        $data = json_encode($this->get('serializer')->serialize($delusario, 'json'), true);

        $em = $this->getDoctrine()->getManager();
        $em->remove($delusario);
        $em->flush();

        return $this->redirectToRoute('usuario');
    }


}
