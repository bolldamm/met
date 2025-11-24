<?php
	class PayPal {
	    
	    var $config = array();
	    var $params = array();
	    var $encrypt = null;
	    
	    function PayPal($config= array()) {
	        $this->config = $config;
	        $this->add('cert_id', $config['cert_id']);
	        $this->add('business', $config['business']);
	    }
	    
	    function add($name='', $value='') {
	        if($name) $this->params[$name] = $value;
	    }
	    
	    function command() {
	        return    $this->config['openssl'].' smime -sign -signer '.$this->config['my_cert'].' -inkey '.$this->config['my_key'].
	                ' -outform der -nodetach -binary | '.$this->config['openssl'].' smime -encrypt'.
	                ' -des3 -binary -outform pem '.$this->config['paypal_cert'];
	    }
	    
	    function openssl() {
	        $descriptors = array(
	               0 => array('pipe', 'r'),
	            1 => array('pipe', 'w'),
	        );
	        $process = proc_open($this->command(), $descriptors, $pipes);
	        if(is_resource($process)) {
	            foreach($this->params as $key => $value) { 
	                fwrite($pipes[0], "$key=$value\n"); 
	            }
	            fflush($pipes[0]);
	            fclose($pipes[0]);
	            $output = array();
	            while (!feof($pipes[1])) {
	                $output[] = fgets($pipes[1]);
	            }
	            fclose($pipes[1]); 
	            proc_close($process);
	            return join('', $output);
	
	        }
	        return false;
	    }
	    
	    function encrypt($params=array()) {
	        $this->params = array_merge($this->params, $params);
	        $this->encrypt = $this->openssl();
	        return $this->encrypt;
	    }
	    
	}
?> 