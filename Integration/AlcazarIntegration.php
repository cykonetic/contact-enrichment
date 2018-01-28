<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Nicholai Bush <nbush@thedmsgrp.com>
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticEnhancerBundle\Integration;

use Mautic\LeadBundle\Entity\Lead;
use Mautic\PluginBundle\Helper\IntegrationHelper;

class AlcazarIntegration extends AbstractEnhancerIntegration
{
    const INTEGRATION_NAME = 'Alcazar';
 
    public function getName()
    {
        return self::INTEGRATION_NAME;
    }
    
    public function getDisplayName()
    {
        return self::INTEGRATION_NAME . ' Data Enhancer';    
    }
        
    public function getAuthenticationType()
    {
        return 'keys';
    }
    
    public function getRequiredKeyFields()
    {
        return [
            'server' => 'mautic.integration.alcazar.server.label',
            'apikey' => 'mautic.integration.alcazar.apikey.label'
        ];
    }
    
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ('features' === $formArea) {
            $builder
                ->add(
                    'output',
                    'choice',
                    [
                        'choices' => [
                            'json' => 'JSON',
                            'xml' => 'XML',
                            'text' => 'text',
                        ],
                        'label' => 'mautic.integration.alcazar.output.label',
                        'data'  =>  isset($data['output']) ? $data['output'] : 'text',
                        'required'    => false,
                        'empty_value' => false,
                        'label_attr'  => ['class' => 'control-label'],
                        'attr'        => [
                            'class' => 'form-control',
                            'tooltip' => 'mautic.integration.alcazar.output.tooltip',
                        ],
                    ]
                )
                ->add(
                    'extended',
                    'yesno_button_group',
                    [
                        'label' => 'mautic.integration.alcazar.extended.label',
                        'data'  => !isset($data['extended']) ? false : $data['extended'],
                        'required'    => false,
                        'empty_value' => false,
                        'label_attr'  => ['class' => 'control-label'],
                        'attr'        => [
                            'class' => 'form-control',
                            'tooltip' => 'mautic.integration.alcazar.extended.tooltip',
                        ],
                    ]
                )
                ->add(
                    'ani',
                    'yesno_button_group',
                    [
                        'label' => 'mautic.integration.alcazar.ani.label',
                        'data'  => !isset($data['ani']) ? false : $data['ani'],
                        'required'    => false,
                        'empty_value' => false,
                        'label_attr'  => ['class' => 'control-label'],
                        'attr'        => [
                            'class' => 'form-control',
                            'tooltip' => 'mautic.integration.alcazar.ani.tooltip',
                        ],
                    ]
                )
                ->add(
                    'dnc',
                    'yesno_button_group',
                    [
                        'label' => 'mautic.integration.alcazar.dnc.label',
                        'data'  => !isset($data['dnc']) ? false : $data['dnc'],
                        'required'    => false,
                        'empty_value' => false,
                        'label_attr'  => ['class' => 'control-label'],
                        'attr'        => [
                            'class' => 'form-control',
                            'tooltip' => 'mautic.integration.alcazar.dnc.tooltip',
                        ],
                    ]
                );       
        }
    }
             
    protected function getEnhancerFieldArray()
    {
        $field_list = ['alcazar_lrn' => ['label' => 'LRN']];
        
        
        $integration = $this->getIntegrationSettings();
        $feature_settings = $integration->getFeatureSettings();
        if ($feature_settings['extended']) {        
            $field_list += $this->getExtendedFields();
        }
        
        return $field_list;
    }
    
    private function getExtendedFields()
    {
        return [
            'alcazar_spid'     => ['label' => 'SPID'],
            'alcazar_ocn'      => ['label' => 'OCN'],
            'alcazar_lata'     => ['label' => 'LATA'],
            'alcazar_city'     => ['label' => 'CITY'],
            'alcazar_state'    => ['label' => 'STATE'],
            'alcazar_lec'      => ['label' => 'LEC'],
            'alcazar_linetype' => ['label' => 'LINETYPE'],
            'alcazar_dnc'      => ['label' => 'DNC'],
            'alcazar_jurisdiction' => [
                                        'label' => 'JURISDICTION',
                                        'default_value' => 'INDETERMINATE',
                                      ],
        ];
    }

    public function doEnhancement(Lead $lead)
    {
        $keys = $this->getDecryptedApiKeys();
          
        $params = ['key' => $keys['apikey']];
        $params['tn'] = $lead->getPhone();
        
        $integration = $this->getIntegrationSettings();
        $params += $integration->getFeatureSettings();
        if (!$params['ani']) {
            unset($params['ani']);
        }
      
        $response = $this->makeRequest(
            $keys['server'],
            ['append_to_query' => $params]
        );      
                   
    }
}

