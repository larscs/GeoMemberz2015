<?php
    header("Cache-Control: max-age=0,no-cache,no-store,post-check=0,pre-check=0");
    require_once 'core/init.php';                         // Must be present on all pages using settings and classes
    if(!Session::isLoggedIn()) {Session::redirect(404);}  // Use this on all pages requiring the user to be logged in


    $memb = Members::getInstance();						            // Use this on all pages using the database
    if(Input::exists('get')) {
        
        if(!Input::getGet('q')) {
            // No query, just return
            exit;
        }
        
        // We have a query, search through details in this order:
        // 1. Member no. - only if numeric
        // 2. username
        // 3. gcnick
        // 4. firstname
        // 5. middlename
        // 6. lastname
        $q = Input::getGet('q');
        $resarray=Array();
        if(is_numeric($q)) {
            if($members=$memb->runSearchQuery("membernum","=",$q)) {
                // result, dump it
                $resarray=addHits($members, $resarray);
            } 
        }
        // If we have a result on member number, we don't need to check the others
        if(sizeof($resarray)<1) {
            if($members=$memb->runSearchQuery("username","LIKE","%".$q."%")) {       // then contains
                $resarray=addHits($members, $resarray);
            } 
            if($members=$memb->runSearchQuery("gcnick","LIKE","%".$q."%")) {       // then contains
                $resarray=addHits($members, $resarray);
            }
            if($members=$memb->runSearchQuery("firstname","LIKE","%".$q."%")) {       // then contains
                $resarray=addHits($members, $resarray);
            }
            if($members=$memb->runSearchQuery("middlename","LIKE","%".$q."%")) {       // then contains
                $resarray=addHits($members, $resarray);
            }
            if($members=$memb->runSearchQuery("lastname","LIKE","%".$q."%")) {       // then contains
                $resarray=addHits($members, $resarray);
            } 
        }
        if(sizeof($resarray)>0){
            echo json_encode($resarray);
        }
    }
        
    // get querystring "q" and search in member list, on nick, name etc.
    //$myarray=Array("Option1","Option2","Option3");
    //echo json_encode($myarray);
    function addHits($members, $resarray) {
            if(sizeof($members)>0) {
                foreach($members as $member) {
                    $userarray=Array();
                    $userarray["username"]=$member["username"];
                    $userarray["memberdetails"]=buildString($member);
                    if (array_search($userarray,$resarray,true)===false) {
                        if(sizeof($resarray)+sizeof($userarray)<12) {
                            array_push($resarray,$userarray);
                        } elseif(sizeof($resarray)+sizeof($userarray)==12) {
                            $userarray["username"]=0;
                            $userarray["memberdetails"]='<div class="suggestion overflow" data-username="0">Too many hits - displaying the first 10</div>';
                            array_push($resarray,$userarray);
                        }
                    }
                }
            } 
        return $resarray;       
    }
    function buildString($member) {
        // build string
        $namestring = $member["gcnick"];
        if($member["gcnick"]!=$member["username"]) {
            $namestring .= " (".$member["username"].")";
        }
        $namestring .= " - ".$member["firstname"];
        if($member["middlename"]) {
            $namestring .= " ".$member["middlename"];
        }
        $namestring .= " ".$member["lastname"];
        if(!$member["active"]) {
            $nameclass = " frozen";
        } else {
            $nameclass = "";
        }
        // <div class="suggestion" onclick="selectSugg(this)" data-username="'+value.username+'">' + value.memberdetails + '</div>
        $result = '<div class="suggestion'.$nameclass.'" onclick="selectSugg(this)" data-username="'.$member["username"].'">'.$namestring.'</div>';
        return $result;
    }