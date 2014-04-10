<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * The analytics controller
 */
class AnalyticsController extends Controller
{
    /**
     * @Route("/setToken/", name="KunstmaanAdminBundle_setToken")
     *
     * @param Request $request
     *
     * @return array
     */
    public function setTokenAction(Request $request)
    {
        $code = $request->query->get('code');

        if (isset($code)) {
            // get API client
            try {
                $googleClientHelper = $this->container->get('kunstmaan_admin.googleclienthelper');
            } catch (\Exception $e) {
                // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
                $currentRoute  = $request->attributes->get('_route');
                $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
                $params['url'] = $currentUrl . 'analytics/setToken/';

                return $this->render('KunstmaanAdminBundle:Analytics:connect.html.twig', $params);
            }

            $googleClientHelper->getClient()->authenticate();
            $googleClientHelper->saveToken($googleClientHelper->getClient()->getAccessToken());

            return $this->redirect($this->generateUrl('KunstmaanAdminBundle_PropertySelection'));
        }

        return $this->redirect($this->generateUrl('KunstmaanAdminBundle_homepage'));
    }

    /**
     * @Route("/selectWebsite", name="KunstmaanAdminBundle_PropertySelection")
     *
     * @param Request $request
     *
     * @return array
     */
    public function propertySelectionAction(Request $request)
    {
        // get API client
        try {
            $googleClientHelper = $this->container->get('kunstmaan_admin.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute  = $request->attributes->get('_route');
            $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';

            return $this->render('KunstmaanAdminBundle:Analytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('properties')) {
            $parts = explode("::", $request->request->get('properties'));
            $googleClientHelper->saveAccountId($parts[1]);
            $googleClientHelper->savePropertyId($parts[0]);

            return $this->redirect($this->generateUrl('KunstmaanAdminBundle_homepage'));
        }

        /** @var GoogleClientHelper $googleClient */
        $googleClient    = $googleClientHelper->getClient();
        $analyticsHelper = $this->container->get('kunstmaan_admin.googleanalyticshelper');
        $analyticsHelper->init($googleClientHelper);
        $properties = $analyticsHelper->getProperties();

        return $this->render(
          'KunstmaanAdminBundle:Analytics:propertySelection.html.twig',
          array('properties' => $properties)
        );
    }

    /**
     * Return an ajax response
     *
     * @Route("/getOverview/{id}", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_analytics_overview_ajax")
     *
     */
    public function getOverviewAction($id)
    {
        if ($id) {
            $em       = $this->getDoctrine()->getManager();
            $overview = $em->getRepository('KunstmaanAdminBundle:AnalyticsOverview')->getOverview($id);

            $extra['trafficDirectPercentage']       = $overview->getTrafficDirectPercentage();
            $extra['trafficReferralPercentage']     = $overview->getTrafficReferralPercentage();
            $extra['trafficSearchEnginePercentage'] = $overview->getTrafficSearchEnginePercentage();
            $extra['dayData']                       = json_decode($overview->getDayData());

            $extra['referrals'] = array();
            foreach ($overview->getReferrals()->toArray() as $key => $referral) {
                $extra['referrals'][$key]['visits'] = $referral->getVisits();
                $extra['referrals'][$key]['name']   = $referral->getName();
            }
            $extra['searches'] = array();
            foreach ($overview->getSearches()->toArray() as $key => $search) {
                $extra['searches'][$key]['visits'] = $search->getVisits();
                $extra['searches'][$key]['name']   = $search->getName();
            }

            $overviewData = array(
              'dayData'             => $overview->getDayData(),
              'useDayData'          => $overview->getUseDayData(),
              'title'               => $overview->getTitle(),
              'timespan'            => $overview->getTimespan(),
              'startOffset'         => $overview->getStartOffset(),
              'visits'              => $overview->getVisits(),
              'returningVisits'     => $overview->getReturningVisits(),
              'newVisits'           => $overview->getNewVisits(),
              'pageViews'           => $overview->getPageViews(),
              'trafficDirect'       => $overview->getTrafficDirect(),
              'trafficReferral'     => $overview->getTrafficReferral(),
              'trafficSearchEngine' => $overview->getTrafficSearchEngine(),
            );

            $return = array(
              'responseCode' => 200,
              'overview'     => $overviewData,
              'extra'        => $extra
            );
        } else {
            $return = array(
              'responseCode' => 400
            );
        }

        return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Return an ajax response
     *
     * @Route("/getDailyOverview", name="KunstmaanAdminBundle_analytics_dailyoverview_ajax")
     *
     */
    public function getDailyOverviewAction()
    {
        $em            = $this->getDoctrine()->getManager();
        $dailyOverview = $em->getRepository('KunstmaanAdminBundle:AnalyticsDailyOverview')->getOverview();

        $return = array(
          'responseCode'  => 200,
          'dailyOverview' => json_decode($dailyOverview->getData())
        );

        return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
    }

}
