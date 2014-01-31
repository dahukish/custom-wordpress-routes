<?php namespace Routes;

use Utility\Service\DI;
use Utility\Server\Request;
use Utility\Server\Response;
use Input\Form;

class Router
{

    private static $services = array();

    private static function preLoad()
    {
        static::$services['DI'] = new DI();

        static::$services['DI']['responseObj'] = function () {
            return new Response();
        };

        static::$services['DI']['query_args'] = function () {
            return array(
                'post_type'=>'doctors',
                'posts_per_page'=>-1,
                'post_status'=>'publish',
            );
        };

        static::$services['DI']['postMetaHandler'] = function () {
            return function ($post_id) {
                $fields = array(
                    'ecpt_education',
                    'ecpt_diagnosticspecialties',
                );

                $array = array();

                foreach ($fields as $field) {
                    $array[$field] = get_post_meta($post_id, $field, true);
                }

                return $array;
            };
        };

        static::$services['DI']['inputFactory'] = function () {
            return function (Request $request) {
                return new Form($request);
            };
        };

        static::$services['DI']['filterCityState'] = function () {
            return function ($val) {
                if (preg_match('/\d{5}/', $val)) {
                    return true;
                } else {
                    if (preg_match('/^[\w\s]+,\s\w\w$/i', $val)) {
                        return true;
                    }
                }

                return false;
            };
        };

        static::$services['DI']['factoryWPDB'] = function ($c) {
            return function ($keyword, $post_type='doctors') use ($c) {
                global $wpdb;

                $sql = "SELECT p.ID FROM ".$wpdb->posts." p ";
                $sql .= "WHERE p.post_status = 'publish' AND p.post_type='".$post_type."' ";
                $sql .= "AND (p.post_content LIKE '%".$keyword."%') ";

                return $wpdb->get_results($sql);

            };
        };

        static::$services['DI']['postQueryFilter'] = function ($c) {
            return function (Request $request, array $query_args, $post_type='doctors') use ($c) {
                $factory = $c['inputFactory'];

                $input = $factory($request);

                if ($input->getValue('city')) {
                    $city_state = $input->getValue('city');

                    $city_filter = $c['filterCityState'];

                    if ($city_filter($city_state) === false) {
                        $base  = get_site_url();
                        wp_redirect($base.'/doctors/find-a-physician/?form_errors=1'); exit;
                    } else {
                        $factoryWPDB = $c['factoryWPDB'];
                        $res = $factoryWPDB($city_state, $post_type);

                        if (!empty($res)) {
                            $query_args['post__in'] = array();

                            foreach ($res as $r) {
                                $query_args['post__in'][] = $r->ID;
                            }

                            return $query_args;
                        }
                    }

                } else {
                    if ($input->getValue('region')) {
                        $region = $input->getValue('region');
                        $base  = get_site_url();
                        wp_redirect($base.$region); exit;
                    }
                }

                return false;
            };
        };

        static::$services['DI']['queryFilter'] = function ($c) {
            return function (Request $request, array $query_args) use ($c) {

                if (($post_token = $request->getToken('([-\w]+)\/find-a-physician')) !== false) {

                    $query_args['post_type'] = $post_token;

                    if ($request->getMethod() === 'POST') {
                        $postQueryFilter = $c['postQueryFilter'];

                        return $postQueryFilter($request, $query_args, $post_token);
                    }

                    return $query_args;
                } else {
                    if (($region_token = $request->getToken('([-\w]+)-providers')) !== false) {

                        $query_args['tax_query'] = array(
                                array(
                                    'taxonomy' => 'regions',
                                    'field' => 'slug',
                                    'terms' => $region_token
                                )
                        );

                        $query_args['post_type'] = $post_token;

                        if (($post_token = $request->getToken('providers\/([-\w]+)')) !== false) {
                            $query_args['post_type'] = $post_token;
                        }

                        return $query_args;
                    }
                }

                return $query_args;
            };
        };
    }

    public static function loadRoutes()
    {
        # hack to setup services
        static::preLoad();

        # get our request from the $_SERVER super global
        $request = Request::getRequest();

        $args = array(
                'responseObj',
                'query_args',
                'queryFilter',
                'postMetaHandler',
            );

        $args_light = array(
                'responseObj',
            );

        $args_ajax = array( 'inputFactory' );

        $controllerLight = function ($responseObj) {
            $responseObj->setHeader('HTTP/1.0 200 OK');
            $responseObj->setTemplateData(array('title'=>'Find A Physician'));
            $responseObj->setTemplate(CDS_PLUGIN_BASE.'/doctor_search/templates/search.php');
            $responseObj->display();
        };

        $contollerNorm = function ($initRequest, $responseObj, $query_args, $queryFilter, $postMetaHandler) {
            global $wp_query;

            $wp_query = new \WP_Query($queryFilter($initRequest, $query_args));

            $responseObj->setHeader('HTTP/1.0 200 OK');
            $responseObj->setTemplateData(array('title'=>'Find a Physician','postMetaHandler'=>$postMetaHandler));
            $responseObj->setTemplate(CDS_PLUGIN_BASE.'/doctor_search/templates/find_a_physician.php');
            $responseObj->display();
        };

        static::matchRoute($request, 'GET', 'doctors/find-a-physician', $controllerLight, $args_light);

        static::matchRoute($request, 'POST', 'doctors/find-a-physician', $contollerNorm, $args, true);

        static::matchRoute($request, 'GET', 'dc-virginia-providers/doctors', $contollerNorm, $args, true);

        static::matchRoute($request, 'GET', 'maryland-providers/doctors', $contollerNorm, $args, true);

        static::matchRoute($request, 'GET', 'newyork-providers/doctors', $contollerNorm, $args, true);

        static::matchRoute($request, 'GET', 'doctors/all', $contollerNorm, $args, true);

        static::matchRoute($request, 'GET', 'doctor-locations/add', function ($request, $inputFactory) {
            $input = $inputFactory($request);
            var_dump($input);
            die('boom1');
        }, $args_ajax, true);

        static::matchRoute($request, 'GET', 'doctor-locations/save', function ($request, $inputFactory) {
            $input = $inputFactory($request);
            var_dump(func_get_args());
            die('boom2');
        }, $args_ajax, true);

        static::matchRoute($request, 'GET', 'doctor-locations/remove', function ($request, $inputFactory) {
            $input = $inputFactory($request);
            var_dump(func_get_args());
            die('boom3');
        }, $args_ajax, true);

    }

    private static function matchRoute(Request $request, $http_verb, $route_path, $call_back, array $inject = array(), $inc_request=false)
    {
        if ($request->equalsHttpVerb($http_verb) && $request->equalsUri($route_path)) {
            if ($call_back instanceof \Closure) {
                if (!empty($inject)) {
                    # get dependancies and inject them
                    $dep = static::$services['DI']->get($inject); # TODO fix this coupling...

                    # inject the original request object into the the closure
                    if($inc_request) array_unshift($dep, $request);

                    call_user_func_array($call_back, $dep);
                } else $call_back();
            }
        }
    }

}
