<?php

  Class DataTable {

    public static function array_to_table($array = []){
      $classes = implode(" ", [ "table", "table-sm", "table-condensed", "table-bordered", "table-striped", "table-hover", "display", "nowrap" ]);
      $html = "<table class=\"table table-sm table-xsm table-condensed table-striped table-hover table-responsive\" >";
      $columns = "";
      foreach ( $array[0] as $key => $column ){
        $name = ucfirst(Helper::translate($key));
        $columns .= "<th class=\"text-center\" >{$name}</th>";
      }
      $lines = "";
      $contador = 0;
      foreach ( $array as $key => $data ){
        $teste = "";
        if ( $contador % 2 > 0)
          $teste = "bg-warning";
        $lines .= "<tr>";
        foreach ( $data as $key2 => $column ){
          $column = Helper::translate($column);
          $lines .= "<td class='text-center {$teste}' >{$column}</td>";
        }
        $lines .= "</tr>";
        $contador++; 
      }

      $html .= "<thead><tr>{$columns}</tr></thead>";
      $html .= "<tbody>{$lines}</tbody>";
      $html .= "</table>";

      return $html;
    }

    public static function create($parameters = [] ){
      $classes = implode(" ", [ "table", "table-sm", "table-condensed", "table-bordered", "table-striped", "table-hover", "display", "nowrap" ]);
      $html = "<table class=\"table table-sm table-xsm table-condensed table-striped table-hover table-responsive\" >";
      $columns = "";
      foreach ( $parameters['columns'] as $key => $column ){
        $name = ucfirst(Helper::translate($column));
        $columns .= "<th class=\"text-center\" >{$name}</th>";
      }
      
      $lines = "";
      foreach ( $parameters['items'] as $key => $data ){
        $temp = "<tr>"; 
          foreach ( $data as $key2 => $column ){
            $attributes = ( isset($column['attributes']) ? Helper::create_html_custom_attributes($column['attributes']) : "" );
            $classes = ( isset($column['classes']) ? Helper::create_html_custom_classes($column['classes']) : "" );

            if ( isset($column['format']) && !empty($column['format']) )
              $column['text'] = Helper::format($column['format'], $column['text']);
              
            $temp .= "<td class=\"{$classes}\" {$attributes} >{$column['text']}</td>";
          }
        $temp .= "</tr>";
        $lines .= $temp;
      }
      $html .= "<thead><tr>{$columns}</tr></thead>";
      $html .= "<tbody>{$lines}</tbody>";
      $html .= "</table>";

      return $html;
    }
  }