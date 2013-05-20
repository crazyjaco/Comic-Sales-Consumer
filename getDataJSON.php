<?php

require('./lib/simple_html_dom.php');

$source_url = "http://www.icv2.com/articles/news/25462.html";

$html = 'test';

//$html = file_get_html($source_url)->plaintext;
$html = file_get_html($source_url);

//print_r($html);
$table_title = array();
$headings = array();
$items = array();
// Loop through returned tables
$table_index = 0;
foreach ( $html->find('table.MsoNormalTable') as $table ) {
	$table_title[ $table_index ] = "";
	$row_index = 0;
	// Loop through returned rows
	foreach( $table->find('tr') as $row ) {
		$cell_index = 0;
		switch ( $row_index ) {
			case 0:  // Table Title
				// Loop through each field
				foreach ( $row->find('td') as $cell ) {
					$table_title[ $table_index ] .= trim(str_replace( "&nbsp;", "", $cell->plaintext ) );
				}
				$table_year_month_arr = explode( " ", trim( $table_title[ $table_index ] ) );
				$table_month = $table_year_month_arr[4];
				$table_year = $table_year_month_arr[5];
				break;
			case 1:  // Table Headings
				// Loop through each field. Build heading array
				$headings[0] = "Year";
				$headings[1] = "Month";
				foreach ( $row->find('td') as $cell ) {
					$headings[ ($cell_index + 2) ] = trim( $cell->plaintext );
					$cell_index++;
				}
				break;
			default:
				// Look through each field. Build data array
				$row_values = array();
				foreach ( $row->find('td') as $cell ) {
					//if(! ($cell_index > 0) ) $items[ $row_index ] = $table_year . ',' . $table_month;
					if(! ($cell_index > 0) ) {
						$row_values[] = $table_year;
						$row_values[] = $table_month;
					}
					$row_values[] = trim( str_replace( "&nbsp;", "", str_replace( ",", "", $cell->plaintext ) ) );
					$cell_index = $cell_index + 1;
				}
				array_push($items, $row_values);
				break;
		}
		//print_r( $row->plaintext );
		//print('<br/>');
		$row_index++;
	}
	$table_index++;
}

$data = array();
$data['headings'] = $headings;
$data['values'] = $items;

//print_r( $table_title ) . '<br/><br/><br/>';
//var_dump( $headings ) . '<br/><br/><br/>';
echo json_encode( $data );
die();
//print_r($table);

// This works but the values object creates an array of strings instead of an array of arrays
//			default:
//				// Look through each field. Build data array
//				$row_values = array();
//				foreach ( $row->find('td') as $cell ) {
//					if(! ($cell_index > 0) ) $items[ $row_index ] = $table_year . ',' . $table_month;
//					$items[ $row_index ] .= "," . trim( str_replace( "&nbsp;", "", str_replace( ",", "", $cell->plaintext ) ) );
//					$cell_index = $cell_index + 1;
//				}
//				break;

?>