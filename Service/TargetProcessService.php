<?php

namespace ZENben\Bundle\TargetProcessBundle\Service;

use ZENben\Bundle\TargetProcessBundle\Buzz\Listener\TargetProcessListener;

class TargetProcessService
{
    protected $browser;
    protected $user;
    protected $defaultProject;
    
    public function __construct(\Buzz\Browser $browser, $securityContext, $config) {
        $browser->addListener(
            new TargetProcessListener(
                $config['api_base_url'], 
                $config['username'], 
                $config['password']
            )
        );
        
        $this->defaultProject = $config['default_project_id'];
        $this->browser = $browser;
        $this->user = $securityContext->getToken()->getUser();
    }
    
    public function addRequest($title, $description, $project = null) {
        $project = $project === null ? $this->defaultProject : $project;
        $this->browser->post('/Requests',[],[
            'Name' => $title,
            'Description' => $description,
            'Project' => ['Id' => $project]
        ]);
    }
    
}