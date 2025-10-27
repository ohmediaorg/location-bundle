<?php

namespace OHMedia\LocationBundle\Controller;

use Doctrine\DBAL\Connection;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\LocationBundle\Entity\Location;
use OHMedia\LocationBundle\Form\LocationType;
use OHMedia\LocationBundle\Repository\LocationRepository;
use OHMedia\LocationBundle\Security\Voter\LocationVoter;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class LocationController extends AbstractController
{
    private const CSRF_TOKEN_REORDER = 'location_reorder';

    public function __construct(private LocationRepository $locationRepository)
    {
    }

    #[Route('/locations', name: 'location_index', methods: ['GET'])]
    public function index(): Response
    {
        $newLocation = new Location();

        $this->denyAccessUnlessGranted(
            LocationVoter::INDEX,
            $newLocation,
            'You cannot access the list of locations.'
        );

        $locations = $this->locationRepository->findAllOrdered();

        return $this->render('@OHMediaLocation/location/location_index.html.twig', [
            'locations' => $locations,
            'new_location' => $newLocation,
            'attributes' => $this->getAttributes(),
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/locations/reorder', name: 'location_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(
            LocationVoter::INDEX,
            new Location(),
            'You cannot reorder the locations.'
        );

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $locations = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($locations as $ordinal => $id) {
                $location = $this->locationRepository->find($id);

                if ($location) {
                    $location->setOrdinal($ordinal);

                    $this->locationRepository->save($location, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/location/create', name: 'location_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $location = new Location();

        $this->denyAccessUnlessGranted(
            LocationVoter::CREATE,
            $location,
            'You cannot create a new location.'
        );

        $form = $this->createForm(LocationType::class, $location);

        $form->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->save($location);

                $this->addFlash('notice', 'The location was created successfully.');

                return $this->redirectToRoute('location_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaLocation/location/location_create.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
        ]);
    }

    #[Route('/location/{id}/edit', name: 'location_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] Location $location,
    ): Response {
        $this->denyAccessUnlessGranted(
            LocationVoter::EDIT,
            $location,
            'You cannot edit this location.'
        );

        $form = $this->createForm(LocationType::class, $location);

        $form->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->save($location);

                $this->addFlash('notice', 'The location was updated successfully.');

                return $this->redirectToRoute('location_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaLocation/location/location_edit.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
        ]);
    }

    private function save(Location $location): void
    {
        if ($location->isPrimary()) {
            $primary = $this->locationRepository->findPrimary();

            if ($primary && $primary !== $location) {
                $primary->setPrimary(false);

                $this->locationRepository->save($primary, true);
            }
        }

        $this->locationRepository->save($location, true);
    }

    #[Route('/location/{id}/delete', name: 'location_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] Location $location,
    ): Response {
        $this->denyAccessUnlessGranted(
            LocationVoter::DELETE,
            $location,
            'You cannot delete this location.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->locationRepository->remove($location, true);

                $this->addFlash('notice', 'The location was deleted successfully.');

                return $this->redirectToRoute('location_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaLocation/location/location_delete.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
        ]);
    }

    private function getAttributes(): array
    {
        return [
            'create' => LocationVoter::CREATE,
            'delete' => LocationVoter::DELETE,
            'edit' => LocationVoter::EDIT,
        ];
    }
}
