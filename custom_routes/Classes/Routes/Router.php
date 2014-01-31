<?php namespace Routes;

use Utility\Service\DI;
use Utility\Server\Request;
use Utility\Server\Response;
use Input\Form;
use Helper\Validation;

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

        static::$services['DI']['validationFactory'] = function() {
            return function(Form $input) {
                return new Validation($input);
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

        static::$services['DI']['locationTemplateFactory'] = function($c) {
            return function($itemCount) {
                $itemCount++;
                $html = '<tr id="ecpt_field_'.$itemCount.'" data-scope="location_item" data-id="'.$itemCount.'">';
                $html .= '<th style="width:20%">';
                $html .= '<label>Address '.$itemCount.'</label>';
                $html .= '</th>';
                $html .= '<td>';
                $html .= '<div class="ecpt_repeatable_wrapper">';
                $html .= '<label for="dcbs_street_'.$itemCount.'">Street</label>';
                $html .= '<input type="text" class="" name="dcbs_street_'.$itemCount.'" id="dcbs_street_'.$itemCount.'" value="" size="30" style="width:80%">';
                $html .= '<label for="dcbs_city_'.$itemCount.'">City</label>';
                $html .= '<input type="text" class="" name="dcbs_city_'.$itemCount.'" id="dcbs_city_'.$itemCount.'" value="" size="30" style="width:80%">';
                $html .= '<label for="dcbs_state_'.$itemCount.'">State</label>';
                $html .= '<select name="dcbs_state_'.$itemCount.'" id="dcbs_state_'.$itemCount.'">';
                $html .= '<option value="MD">Maryland</option><option value="NY">New York</option><option value="DC">District of Columbia</option><option value="VA">Virginia</option>';
                $html .= '</select>';
                $html .= '<label for="dcbs_zipcode_'.$itemCount.'">Zipcode</label>';
                $html .= '<input type="text" class="" name="dcbs_zipcode_'.$itemCount.'" id="dcbs_zipcode_'.$itemCount.'" value="" size="30" style="width:80%">';
                $html .= '<a href="http://treatingpain.staging.wpengine.com/wp-admin/doctor-locations/remove" class="button-secondary" data-action="remove-location">x</a>';
                $html .= '<a href="http://treatingpain.staging.wpengine.com/wp-admin/doctor-locations/save" class="button-secondary" data-action="save-location">Save Location</a>';
                $html .= '</div>';
                $html .= '</td>';
                $html .= '</tr>';
                return $html;
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

        static::matchRoute($request, 'POST', 'wp-admin/doctor-locations/add', function ($request, $inputFactory, $locationTemplateFactory) {
            $input = $inputFactory($request);
            header('HTTP/1.0 200 OK');
            $result = array('status' => true);
            $result['msg'] = $locationTemplateFactory($input->getValue('itemCount'));
            echo json_encode($result);
            exit;
        }, array_merge($args_ajax, array('locationTemplateFactory')), true);

        static::matchRoute($request, 'POST', 'wp-admin/doctor-locations/save', function ($request, $inputFactory, $validationFactory) {
            header('HTTP/1.0 200 OK');
            $input = $inputFactory($request);
            $val = $validationFactory($input);

            if($val->isValid()) {
                global $wpdb;
                $result = array('status'=>true, 'msg'=>'recorded added successful.');
            } else {
                $result = array('status'=>false, 'msg'=>$val->errors());
            }
            echo json_encode($result);
            exit;
        }, array_merge($args_ajax, array('validationFactory')), true);

        static::matchRoute($request, 'POST', 'wp-admin/doctor-locations/remove', function ($request, $inputFactory) {
            header('HTTP/1.0 200 OK');
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
                    if ($inc_request) {
                        array_unshift($dep, $request);
                    }
                    // var_dump($dep); exit;
                    call_user_func_array($call_back, $dep);
                } else {
                    $call_back();
                }
            }
        }
    }

}
