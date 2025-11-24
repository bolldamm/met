<?php
      if(strlen($firstName)<9) {
        $charSize = "font-size: 60px;";
      } else {
        $charSize = "font-size: ".ceil((strlen($firstName) * -2) + 76)."px;";
      }
      $firstName = str_replace(" ", "&nbsp;", $firstName);

      if(strlen($lastName)<13) {
        $charSize2 = "font-size: 48px;";
      } else if(strlen($lastName)<26) {
        $charSize2 = "font-size: ".ceil((strlen($lastName) * -2.4) + 77)."px;";
      } else {
        $charSize2 = "font-size: 16px;";
      }
      $lastName = str_replace(" ", "&nbsp;", $lastName);
?>