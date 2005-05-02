<?php
/*=======================================================================
// File:	JPGRAPH_PIE.PHP
// Description:	Pie plot extension for JpGraph
// Created: 	2001-02-14
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id$
//
// License:	This code is released under QPL
// Copyright (C) 2001,2002,2003 Johan Persson
//========================================================================
*/


// Defines for PiePlot::SetLabelType()
DEFINE("PIE_VALUE_ABS",1);
DEFINE("PIE_VALUE_PER",0);
DEFINE("PIE_VALUE_PERCENTAGE",0);
DEFINE("PIE_VALUE_ADJPERCENTAGE",2);
DEFINE("PIE_VALUE_ADJPER",2);

//===================================================
// CLASS PiePlot
// Description: Draws a pie plot
//===================================================
class PiePlot {
    var $posx=0.5,$posy=0.5;
    var $radius=0.3;
    var $explode_radius=array(),$explode_all=false,$explode_r=20;
    var $labels=null, $legends=null;
    var $csimtargets=null;  // Array of targets for CSIM
    var $csimareas='';		// Generated CSIM text	
    var $csimalts=null;		// ALT tags for corresponding target
    var $data=null;
    var $title;
    var $startangle=0;
    var $weight=1, $color="black";
    var $legend_margin=6,$show_labels=true;
    var $themearr = array(
	"earth" 	=> array(136,34,40,45,46,62,63,134,74,10,120,136,141,168,180,77,209,218,346,395,89,430),
	"pastel" => array(27,415,128,59,66,79,105,110,42,147,152,230,236,240,331,337,405,38),
	"water"  => array(8,370,24,40,335,56,213,237,268,14,326,387,10,388),
	"sand"   => array(27,168,34,170,19,50,65,72,131,209,46,393));
    var $theme="earth";
    var $setslicecolors=array();
    var $labeltype=0; // Default to percentage
    var $pie_border=true,$pie_interior_border=true;
    var $value;
    var $ishadowcolor='',$ishadowdrop=4;
    var $ilabelposadj=1;
    var $legendcsimtargets = array();
    var $legendcsimalts = array();
    var $adjusted_data = array();
	
//---------------
// CONSTRUCTOR
    function PiePlot($data) {
	$this->data = array_reverse($data);
	$this->title = new Text("");
	$this->title->SetFont(FF_FONT1,FS_BOLD);
	$this->value = new DisplayValue();
	$this->value->Show();
	$this->value->SetFormat('%.1f%%');
    }

//---------------
// PUBLIC METHODS	
    function SetCenter($x,$y=0.5) {
	$this->posx = $x;
	$this->posy = $y;
    }

    function SetColor($aColor) {
	$this->color = $aColor;
    }
	
    function SetSliceColors($aColors) {
	$this->setslicecolors = $aColors;
    }
	
    function SetShadow($aColor='darkgray',$aDropWidth=4) {
	$this->ishadowcolor = $aColor;
	$this->ishadowdrop = $aDropWidth;
    }

    function SetCSIMTargets($targets,$alts=null) {
	$this->csimtargets=array_reverse($targets);
	if( is_array($alts) )
	    $this->csimalts=array_reverse($alts);
    }
	
    function GetCSIMareas() {
	return $this->csimareas;
    }

    function AddSliceToCSIM($i,$xc,$yc,$radius,$sa,$ea) {  
        //Slice number, ellipse centre (x,y), height, width, start angle, end angle
	while( $sa > 2*M_PI ) $sa = $sa - 2*M_PI;
	while( $ea > 2*M_PI ) $ea = $ea - 2*M_PI;

	$sa = 2*M_PI - $sa;
	$ea = 2*M_PI - $ea;

	//add coordinates of the centre to the map
	$coords = "$xc, $yc";

	//add coordinates of the first point on the arc to the map
	$xp = floor(($radius*cos($ea))+$xc);
	$yp = floor($yc-$radius*sin($ea));
	$coords.= ", $xp, $yp";
	
	//add coordinates every 0.2 radians
	$a=$ea+0.2;
	while ($a<$sa) {
	    $xp = floor($radius*cos($a)+$xc);
	    $yp = floor($yc-$radius*sin($a));
	    $coords.= ", $xp, $yp";
	    $a += 0.2;
	}
		
	//Add the last point on the arc
	$xp = floor($radius*cos($sa)+$xc);
	$yp = floor($yc-$radius*sin($sa));
	$coords.= ", $xp, $yp";
	if( !empty($this->csimtargets[$i]) ) {
	    $this->csimareas .= "<area shape=\"poly\" coords=\"$coords\" href=\"".
		$this->csimtargets[$i]."\"";
	    if( !empty($this->csimalts[$i]) ) {
		$tmp=sprintf($this->csimalts[$i],$this->data[$i]);
		$this->csimareas .= " alt=\"$tmp\" title=\"$tmp\"";
	    }
	    $this->csimareas .= ">\n";
	}
    }

	
    function SetTheme($aTheme) {
	if( in_array($aTheme,array_keys($this->themearr)) )
	    $this->theme = $aTheme;
	else
	    JpGraphError::Raise("PiePLot::SetTheme() Unknown theme: $aTheme");
    }
	
    function ExplodeSlice($e,$radius=20) {
	if( ! is_integer($e) ) 
	    JpGraphError::Raise('Argument to PiePlot::ExplodeSlice() must be an integer');
	$this->explode_radius[$e]=$radius;
    }

    function ExplodeAll($radius=20) {
	$this->explode_all=true;
	$this->explode_r = $radius;
    }

    function Explode($aExplodeArr) {
	if( !is_array($aExplodeArr) ) {
	    JpGraphError::Raise("Argument to PiePlot::Explode() must be an array with integer distances.");
	}
	$this->explode_radius = $aExplodeArr;
    }

    function SetStartAngle($aStart) {
	if( $aStart < 0 || $aStart > 360 ) {
	    JpGraphError::Raise('Slice start angle must be between 0 and 360 degrees.');
	}
	$this->startangle = 360-$aStart;
	$this->startangle *= M_PI/180;
    }
	
    function SetFont($family,$style=FS_NORMAL,$size=10) {
		JpGraphError::Raise('PiePlot::SetFont() is deprecated. Use PiePlot->value->SetFont() instead.');
    }
	
    // Size in percentage
    function SetSize($aSize) {
	if( ($aSize>0 && $aSize<=0.5) || ($aSize>10 && $aSize<1000) )
	    $this->radius = $aSize;
	else
	    JpGraphError::Raise("PiePlot::SetSize() Radius for pie must either be specified as a fraction
                                [0, 0.5] of the size of the image or as an absolute size in pixels 
                                in the range [10, 1000]");
    }
	
    function SetFontColor($aColor) {
	JpGraphError::Raise('PiePlot::SetFontColor() is deprecated. Use PiePlot->value->SetColor() instead.');
    }
	
    // Set label arrays
    function SetLegends($aLegend) {
	$this->legends = $aLegend;
    }

    // Set text labels for slices 
    function SetLabels($aLabels,$aLblPosAdj="auto") {
	$this->labels = array_reverse($aLabels);
	$this->ilabelposadj=$aLblPosAdj;
    }

    function SetLabelPos($aLblPosAdj) {
	$this->ilabelposadj=$aLblPosAdj;
    }
	
    // Should we display actual value or percentage?
    function SetLabelType($t) {
	if( $t < 0 || $t > 2 ) 
	    JpGraphError::Raise("PiePlot::SetLabelType() Type for pie plots must be 0 or 1 (not $t).");
	$this->labeltype=$t;
    }

    function SetValueType($aType) {
	$this->SetLabelType($aType);
    }

    // Should the circle around a pie plot be displayed
    function ShowBorder($exterior=true,$interior=true) {
	$this->pie_border = $exterior;
	$this->pie_interior_border = $interior;
    }
	
    // Setup the legends
    function Legend(&$graph) {
	$colors = array_keys($graph->img->rgb->rgb_table);
   	sort($colors);	
   	$ta=$this->themearr[$this->theme];	
   	$n = count($this->data);

   	if( $this->setslicecolors==null ) {
	    $numcolors=count($ta);
	    if( get_class($this)==='pieplot3d' ) {
		$ta = array_reverse(array_slice($ta,0,$n));
	    }
	}
   	else {
	    $this->setslicecolors = array_slice($this->setslicecolors,0,$n);
	    $numcolors=count($this->setslicecolors); 
	    if( $graph->pieaa && get_class($this)==='pieplot' ) { 
		$this->setslicecolors = array_reverse($this->setslicecolors);
	    }
	}
		
	$sum=0;
	for($i=0; $i < $n; ++$i)
	    $sum += $this->data[$i];

	// Bail out with error if the sum is 0
	if( $sum==0 )
	    JpGraphError::Raise("Illegal pie plot. Sum of all data is zero for Pie!");

	// Make sure we don't plot more values than data points
	// (in case the user added more legends than data points)
	$n = min(count($this->legends),count($this->data));
	if( $this->legends != "" ) {
	    $this->legends = array_reverse(array_slice($this->legends,0,$n));
	}
	for( $i=$n-1; $i >= 0; --$i ) {
	    $l = $this->legends[$i];
	    // Replace possible format with actual values
	    if( $this->labeltype==0 ) {
		$l = sprintf($l,100*$this->data[$i]/$sum);
		$alt = sprintf($this->csimalts[$i],$this->data[$i]);

	    }
	    elseif( $this->labeltype == 1)  {
		$l = sprintf($l,$this->data[$i]);
		$alt = sprintf($this->csimalts[$i],$this->data[$i]);
	    
	    }
	    else {
		$l = sprintf($l,$this->adjusted_data[$i]);
		$alt = sprintf($this->csimalts[$i],$this->adjusted_data[$i]);
	    }
	    
				
	    if( $this->setslicecolors==null ) {
		$graph->legend->Add($l,$colors[$ta[$i%$numcolors]],"",0,
				    $this->csimtargets[$i],$alt);
	    }
	    else {
		$graph->legend->Add($l,$this->setslicecolors[$i%$numcolors],"",0,
				    $this->csimtargets[$i],$alt);
	    }
	}
    }
	
    // Adjust the rounded percetage value so that the sum of
    // of the pie slices are always 100%
    // Using the Hare/Niemeyer method
    function AdjPercentage($aData,$aPrec=0) {
	$mul=100;
	if( $aPrec > 0 && $aPrec < 3 ) {
	    if( $aPrec == 1 ) 
		$mul=1000;
		else
		    $mul=10000;
	}
	
	$tmp = array();
	$result = array();
	$quote_sum=0;
	$n = count($aData) ;
	for( $i=0, $sum=0; $i < $n; ++$i )
	    $sum+=$aData[$i];
	foreach($aData as $index => $value) {
	    $tmp_percentage=$value/$sum*$mul;
	    $result[$index]=floor($tmp_percentage);
	    $tmp[$index]=$tmp_percentage-$result[$index];
	    $quote_sum+=$result[$index];
	}
	if( $quote_sum == $mul) {
	    if( $mul > 100 ) {
		$tmp = $mul / 100;
		for( $i=0; $i < $n; ++$i ) {
		    $result[$i] /= $tmp ;
		}
	    }
	    return $result;
	}
	arsort($tmp,SORT_NUMERIC);
	reset($tmp);
	for($i=0; $i < $mul-$quote_sum; $i++)
	{
	    $result[key($tmp)]++;
	    next($tmp);
	}
	if( $mul > 100 ) {
	    $tmp = $mul / 100;
	    for( $i=0; $i < $n; ++$i ) {
		$result[$i] /= $tmp ;
	    }
	}
	return $result;
    }


    function Stroke(&$img,$aaoption=0) {
	// aaoption is used to handle antialias
	// aaoption == 0 a normal pie
	// aaoption == 1 just the body
	// aaoption == 2 just the values

	// Explode scaling. If anti anti alias we scale the image
	// twice and we also need to scale the exploding distance
	$expscale = $aaoption === 1 ? 2 : 1;

	if( $this->labeltype == 2 ) {
	    // Adjust the data so that it will add up to 100%
	    $this->adjusted_data = $this->AdjPercentage($this->data);
	}

	$colors = array_keys($img->rgb->rgb_table);
   	sort($colors);	
   	$ta=$this->themearr[$this->theme];	
	$n = count($this->data);
   	
   	if( $this->setslicecolors==null ) {
	    $numcolors=count($ta);
	}
   	else {
	    // We need to create an array of colors as long as the data
	    // since we need to reverse it to get the colors in the right order
	    $numcolors=count($this->setslicecolors); 
	    if( $n > $numcolors ) {
		$i = 2*$numcolors;
		while( $n > $i ) {
		    $this->setslicecolors = array_merge($this->setslicecolors,$this->setslicecolors);
		    $i += $n;
		}
		$tt = array_slice($this->setslicecolors,0,$n % $numcolors);
		$this->setslicecolors = array_merge($this->setslicecolors,$tt);
		$this->setslicecolors = array_reverse($this->setslicecolors);
	    }
	}

	// Draw the slices
	$sum=0;
	for($i=0; $i < $n; ++$i)
	    $sum += $this->data[$i];
	
	// Bail out with error if the sum is 0
	if( $sum==0 )
	    JpGraphError::Raise("Sum of all data is 0 for Pie.");
	
	// Set up the pie-circle
	if( $this->radius <= 1 )
	    $radius = floor($this->radius*min($img->width,$img->height));
	else {
	    $radius = $aaoption === 1 ? $this->radius*2 : $this->radius;
	}

	if( $this->posx <= 1 && $this->posx > 0 )
	    $xc = round($this->posx*$img->width);
	else
	    $xc = $this->posx ;
	
	if( $this->posy <= 1 && $this->posy > 0 )
	    $yc = round($this->posy*$img->height);
	else
	    $yc = $this->posy ;
		
	$n = count($this->data);

	if( $this->explode_all )
	    for($i=0; $i < $n; ++$i)
		$this->explode_radius[$i]=$this->explode_r;

	if( $this->ishadowcolor != "" && $aaoption !== 2) {
	    $accsum=0;
	    $angle2 = $this->startangle;
	    $img->SetColor($this->ishadowcolor);
	    for($i=0; $sum > 0 && $i < $n; ++$i) {
		$j = $n-$i-1;
		$d = $this->data[$i];
		$angle1 = $angle2;
		$accsum += $d;
		$angle2 = $this->startangle+2*M_PI*$accsum/$sum;
		if( empty($this->explode_radius[$j]) )
		    $this->explode_radius[$j]=0;

		$la = 2*M_PI - (abs($angle2-$angle1)/2.0+$angle1);

		$xcm = $xc + $this->explode_radius[$j]*cos($la)*$expscale;
		$ycm = $yc - $this->explode_radius[$j]*sin($la)*$expscale;
		
		$xcm += $this->ishadowdrop*$expscale;
		$ycm += $this->ishadowdrop*$expscale;

		$img->CakeSlice($xcm,$ycm,$radius,$radius,
				$angle1*180/M_PI,$angle2*180/M_PI,$this->ishadowcolor);
		
	    }
	}

	$accsum=0;
	$angle2 = $this->startangle;
	$img->SetColor($this->color);
	for($i=0; $sum>0 && $i < $n; ++$i) {
	    $j = $n-$i-1;
	    if( empty($this->explode_radius[$j]) )
		$this->explode_radius[$j]=0;
	    $d = $this->data[$i];
	    $angle1 = $angle2;
	    $accsum += $d;
	    $angle2 = $this->startangle+2*M_PI*$accsum/$sum;
	    $this->la[$i] = 2*M_PI - (abs($angle2-$angle1)/2.0+$angle1);

	    if( $d == 0 ) continue;

	    if( $this->setslicecolors==null )
		$slicecolor=$colors[$ta[$i%$numcolors]];
	    else
		$slicecolor=$this->setslicecolors[$i%$numcolors];

	    if( $this->pie_interior_border && $aaoption===0 )
		$img->SetColor($this->color);
	    else
		$img->SetColor($slicecolor);

	    $arccolor = $this->pie_border && $aaoption===0 ? $this->color : "";

	    $xcm = $xc + $this->explode_radius[$j]*cos($this->la[$i])*$expscale;
	    $ycm = $yc - $this->explode_radius[$j]*sin($this->la[$i])*$expscale;

	    if( $aaoption !== 2 ) {
		$img->CakeSlice($xcm,$ycm,$radius-1,$radius-1,
				$angle1*180/M_PI,$angle2*180/M_PI,$slicecolor,$arccolor);
	    }

	    if( $this->csimtargets && $aaoption !== 1 ) 
		$this->AddSliceToCSIM($i,$xcm,$ycm,$radius,$angle1,$angle2);
	}

	// Format the titles for each slice
	for( $i=0; $i < $n; ++$i) {
	    if( $this->labeltype==0 ) {
		if( $sum != 0 )
		    $l = 100.0*$this->data[$i]/$sum;
		else
		    $l = 0.0;
	    }
	    elseif( $this->labeltype==1 ) {
		$l = $this->data[$i]*1.0;
	    }
	    else {
		$l = $this->adjusted_data[$i];
	    }
	    if( isset($this->labels[$i]) && is_string($this->labels[$i]) )
		$this->labels[$i]=sprintf($this->labels[$i],$l);
	    else
		$this->labels[$i]=$l;
	}

	if( $this->value->show && $aaoption !== 1 ) {
	    $this->StrokeAllLabels($img,$xc,$yc,$radius);
	}

	// Adjust title position
	if( $aaoption !== 1 ) {
	    $this->title->Pos($xc,
			  $yc-$this->title->GetFontHeight($img)-$radius-$this->title->margin,
			  "center","bottom");
	    $this->title->Stroke($img);
	}

    }

//---------------
// PRIVATE METHODS	

    function StrokeAllLabels($img,$xc,$yc,$radius) {
	$n = count($this->labels);
	for($i=0; $i < $n; ++$i) {
	    $this->StrokeLabel($this->labels[$i],$img,$xc,$yc,
			       $this->la[$i],
			       $radius + $this->explode_radius[$n-$i-1]); 
	}
    }

    // Position the labels of each slice
    function StrokeLabel($label,$img,$xc,$yc,$a,$r) {

	// Default value
	if( $this->ilabelposadj === 'auto' )
	    $this->ilabelposadj = 0.65;

	// We position the values diferently depending on if they are inside
	// or outside the pie
	if( $this->ilabelposadj < 1.0 ) {

	    $this->value->SetAlign('center','center');
	    $this->value->margin = 0;
	    
	    $xt=round($this->ilabelposadj*$r*cos($a)+$xc);
	    $yt=round($yc-$this->ilabelposadj*$r*sin($a));
	    
	    $this->value->Stroke($img,$label,$xt,$yt);
	}
	else {

	    $this->value->halign = "left";
	    $this->value->valign = "top";
	    
	    // Position the axis title. 
	    // dx, dy is the offset from the top left corner of the bounding box that sorrounds the text
	    // that intersects with the extension of the corresponding axis. The code looks a little
	    // bit messy but this is really the only way of having a reasonable position of the
	    // axis titles.
	    $img->SetFont($this->value->ff,$this->value->fs,$this->value->fsize);
	    $h=$img->GetTextHeight($label);
	    // For numeric values the format of the display value
	    // must be taken into account
	    if( is_numeric($label) ) {
		if( $label > 0 )
		    $w=$img->GetTextWidth(sprintf($this->value->format,$label));
		else
		    $w=$img->GetTextWidth(sprintf($this->value->negformat,$label));
	    }
	    else
		$w=$img->GetTextWidth($label);

	    if( $this->ilabelposadj > 1.0 && $this->ilabelposadj < 5.0) {
		$r *= $this->ilabelposadj;
	    }

	    $r += $img->GetFontHeight()/1.5 + $this->value->margin ;
	    $xt=round($r*cos($a)+$xc);
	    $yt=round($yc-$r*sin($a));

	    while( $a < 0 ) $a += 2*M_PI;
	    while( $a > 2*M_PI ) $a -= 2*M_PI;

	    if( $a>=7*M_PI/4 || $a <= M_PI/4 ) $dx=0;
	    if( $a>=M_PI/4 && $a <= 3*M_PI/4 ) $dx=($a-M_PI/4)*2/M_PI; 
	    if( $a>=3*M_PI/4 && $a <= 5*M_PI/4 ) $dx=1;
	    if( $a>=5*M_PI/4 && $a <= 7*M_PI/4 ) $dx=(1-($a-M_PI*5/4)*2/M_PI);
	    
	    if( $a>=7*M_PI/4 ) $dy=(($a-M_PI)-3*M_PI/4)*2/M_PI;
	    if( $a<=M_PI/4 ) $dy=(1-$a*2/M_PI);
	    if( $a>=M_PI/4 && $a <= 3*M_PI/4 ) $dy=1;
	    if( $a>=3*M_PI/4 && $a <= 5*M_PI/4 ) $dy=(1-($a-3*M_PI/4)*2/M_PI);
	    if( $a>=5*M_PI/4 && $a <= 7*M_PI/4 ) $dy=0;
	    
	    $oldmargin = $this->value->margin ;
	    $this->value->margin = 0;
	    $this->value->Stroke($img,$label,$xt-$dx*$w,$yt-$dy*$h);
	    $this->value->margin = $oldmargin ;
	}
    }	
} // Class


//===================================================
// CLASS PiePlotC
// Description: Same as a normal pie plot but with a 
// filled circle in the center
//===================================================
class PiePlotC extends PiePlot {
    var $imidsize=0.5;		// Fraction of total width
    var $imidcolor='white';
    var $midtitle='';
    var $middlecsimtarget="",$middlecsimalt="";

    function PiePlotC($data,$aCenterTitle='') {
	parent::PiePlot($data);
	$this->midtitle = new Text();
	$this->midtitle->ParagraphAlign('center');
    }

    function SetMid($aTitle,$aColor='white',$aSize=0.5) {
	$this->midtitle->Set($aTitle);
	$this->imidsize = $aSize ; 
	$this->imidcolor = $aColor ; 
    }

    function SetMidTitle($aTitle) {
	$this->midtitle->Set($aTitle);
    }

    function SetMidSize($aSize) {
	$this->imidsize = $aSize ; 
    }

    function SetMidColor($aColor) {
	$this->imidcolor = $aColor ; 
    }

    function SetMidCSIM($aTarget,$aAlt) {
	$this->middlecsimtarget = $aTarget;
	$this->middlecsimalt = $aAlt;
    }

    function AddSliceToCSIM($i,$xc,$yc,$radius,$sa,$ea) {  
        //Slice number, ellipse centre (x,y), radius, start angle, end angle
	while( $sa > 2*M_PI ) $sa = $sa - 2*M_PI;
	while( $ea > 2*M_PI ) $ea = $ea - 2*M_PI;

	$sa = 2*M_PI - $sa;
	$ea = 2*M_PI - $ea;

	// Add inner circle first point
	$xp = floor(($this->imidsize*$radius*cos($ea))+$xc);
	$yp = floor($yc-($this->imidsize*$radius*sin($ea)));
	$coords = "$xp, $yp";
	
	//add coordinates every 0.25 radians
	$a=$ea+0.25;
	while ($a < $sa) {
	    $xp = floor(($this->imidsize*$radius*cos($a)+$xc));
	    $yp = floor($yc-($this->imidsize*$radius*sin($a)));
	    $coords.= ", $xp, $yp";
	    $a += 0.25;
	}

	// Make sure we end at the last point
	$xp = floor(($this->imidsize*$radius*cos($sa)+$xc));
	$yp = floor($yc-($this->imidsize*$radius*sin($sa)));
	$coords.= ", $xp, $yp";

	// Straight line to outer circle
	$xp = floor($radius*cos($sa)+$xc);
	$yp = floor($yc-$radius*sin($sa));
	$coords.= ", $xp, $yp";	

	//add coordinates every 0.25 radians
	$a=$sa - 0.25;
	while ($a > $ea) {
	    $xp = floor($radius*cos($a)+$xc);
	    $yp = floor($yc-$radius*sin($a));
	    $coords.= ", $xp, $yp";
	    $a -= 0.25;
	}
		
	//Add the last point on the arc
	$xp = floor($radius*cos($ea)+$xc);
	$yp = floor($yc-$radius*sin($ea));
	$coords.= ", $xp, $yp";

	// Close the arc
	$xp = floor(($this->imidsize*$radius*cos($ea))+$xc);
	$yp = floor($yc-($this->imidsize*$radius*sin($ea)));
	$coords .= ", $xp, $yp";

	if( !empty($this->csimtargets[$i]) ) {
	    $this->csimareas .= "<area shape=\"poly\" coords=\"$coords\" href=\"".
		$this->csimtargets[$i]."\"";
	    if( !empty($this->csimalts[$i]) ) {
		$tmp=sprintf($this->csimalts[$i],$this->data[$i]);
		$this->csimareas .= " alt=\"$tmp\" title=\"$tmp\"";
	    }
	    $this->csimareas .= ">\n";
	}
    }


    function Stroke($img,$aaoption=0) {

	// Stroke the pie but don't stroke values
	$tmp =  $this->value->show;
	$this->value->show = false;
	parent::Stroke($img,$aaoption);
	$this->value->show = $tmp;

 	$xc = round($this->posx*$img->width);
	$yc = round($this->posy*$img->height);

	$radius = floor($this->radius * min($img->width,$img->height)) ;


	if( $this->imidsize > 0 && $aaoption !== 2 ) {

	    if( $this->ishadowcolor != "" ) {
		$img->SetColor($this->ishadowcolor);
		$img->FilledCircle($xc+$this->ishadowdrop,$yc+$this->ishadowdrop,
				   round($radius*$this->imidsize));
	    }

	    $img->SetColor($this->imidcolor);
	    $img->FilledCircle($xc,$yc,round($radius*$this->imidsize));

	    if(  $this->pie_border && $aaoption === 0 ) {
		$img->SetColor($this->color);
		$img->Circle($xc,$yc,round($radius*$this->imidsize));
	    }

	    if( !empty($this->middlecsimtarget) )
		$this->AddMiddleCSIM($xc,$yc,round($radius*$this->imidsize));

	}

	if( $this->value->show && $aaoption !== 1) {
	    $this->StrokeAllLabels($img,$xc,$yc,$radius);
	    $this->midtitle->Pos($xc,$yc,'center','center');
	    $this->midtitle->Stroke($img);
	}

    }

    function AddMiddleCSIM($xc,$yc,$r) {
	$this->csimareas .= "<area shape=\"circle\" coords=\"$xc,$yc,$r\" href=\"".
	    $this->middlecsimtarget."\"";
	if( !empty($this->middlecsimalt) ) {
	    $tmp = $this->middlecsimalt;
	    $this->csimareas .= " alt=\"$tmp\" title=\"$tmp\"";
	}
	$this->csimareas .= ">\n";
    }

    function StrokeLabel($label,$img,$xc,$yc,$a,$r) {

	if( $this->ilabelposadj === 'auto' )
	    $this->ilabelposadj = (1-$this->imidsize)/2+$this->imidsize;

	parent::StrokeLabel($label,$img,$xc,$yc,$a,$r);

    }

}


//===================================================
// CLASS PieGraph
// Description: 
//===================================================
class PieGraph extends Graph {
    var $posx, $posy, $radius;		
    var $legends=array();	
    var $plots=array();
    var $pieaa = false ;
//---------------
// CONSTRUCTOR
    function PieGraph($width=300,$height=200,$cachedName="",$timeout=0,$inline=1) {
	$this->Graph($width,$height,$cachedName,$timeout,$inline);
	$this->posx=$width/2;
	$this->posy=$height/2;
	$this->SetColor(array(255,255,255));		
    }

//---------------
// PUBLIC METHODS	
    function Add($aObj) {

	if( is_array($aObj) && count($aObj) > 0 )
	    $cl = strtolower(get_class($aObj[0]));
	else
	    $cl = strtolower(get_class($aObj));

	if( $cl == 'text' ) 
	    $this->AddText($aObj);
	elseif( $cl == 'iconplot' ) 
	    $this->AddIcon($aObj);
	else {
	    if( is_array($aObj) ) {
		$n = count($aObj);
		for($i=0; $i < $n; ++$i ) {
		    $this->plots[] = &$aObj[$i];
		}
	    }
	    else {
		$this->plots[] = $aObj;
	    }
	}
    }

    function SetAntiAliasing($aFlg=true) {
	$this->pieaa = $aFlg;
    }
	
    function SetColor($c) {
	$this->SetMarginColor($c);
    }


    function DisplayCSIMAreas() {
	    $csim="";
	    foreach($this->plots as $p ) {
		$csim .= $p->GetCSIMareas();
	    }
	    //$csim.= $this->legend->GetCSIMareas();
	    if (preg_match_all("/area shape=\"(\w+)\" coords=\"([0-9\, ]+)\"/", $csim, $coords)) {
		$this->img->SetColor($this->csimcolor);
		for ($i=0; $i<count($coords[0]); $i++) {
		    if ($coords[1][$i]=="poly") {
			preg_match_all('/\s*([0-9]+)\s*,\s*([0-9]+)\s*,*/',$coords[2][$i],$pts);
			$this->img->SetStartPoint($pts[1][count($pts[0])-1],$pts[2][count($pts[0])-1]);
			for ($j=0; $j<count($pts[0]); $j++) {
			    $this->img->LineTo($pts[1][$j],$pts[2][$j]);
			}
		    } else if ($coords[1][$i]=="rect") {
			$pts = preg_split('/,/', $coords[2][$i]);
			$this->img->SetStartPoint($pts[0],$pts[1]);
			$this->img->LineTo($pts[2],$pts[1]);
			$this->img->LineTo($pts[2],$pts[3]);
			$this->img->LineTo($pts[0],$pts[3]);
			$this->img->LineTo($pts[0],$pts[1]);
						
		    }
		}
	    }
    }

    // Method description
    function Stroke($aStrokeFileName="") {
	// If the filename is the predefined value = '_csim_special_'
	// we assume that the call to stroke only needs to do enough
	// to correctly generate the CSIM maps.
	// We use this variable to skip things we don't strictly need
	// to do to generate the image map to improve performance
	// a best we can. Therefor you will see a lot of tests !$_csim in the
	// code below.
	$_csim = ($aStrokeFileName===_CSIM_SPECIALFILE);

	// We need to know if we have stroked the plot in the
	// GetCSIMareas. Otherwise the CSIM hasn't been generated
	// and in the case of GetCSIM called before stroke to generate
	// CSIM without storing an image to disk GetCSIM must call Stroke.
	$this->iHasStroked = true;

	$n = count($this->plots);

	if( $this->pieaa ) {

	    if( !$_csim ) {
		if( $this->background_image != "" ) {
		    $this->StrokeFrameBackground();		
		}
		else {
		    $this->StrokeFrame();		
		}
	    }

	    $w = $this->img->width;
	    $h = $this->img->height;
	    $oldimg = $this->img->img;

	    $this->img->CreateImgCanvas(2*$w,2*$h);
	    
	    $this->img->SetColor( $this->margin_color );
	    $this->img->FilledRectangle(0,0,2*$w-1,2*$h-1);

	    // Make all icons *2 i size since we will be scaling down the
	    // imahe to do the anti aliasing
	    $ni = count($this->iIcons);
	    for($i=0; $i < $ni; ++$i) {
		$this->iIcons[$i]->iScale *= 2 ;
	    }
	    $this->StrokeIcons();

	    for($i=0; $i < $n; ++$i) {
		if( $this->plots[$i]->posx > 1 ) 
		    $this->plots[$i]->posx *= 2 ;
		if( $this->plots[$i]->posy > 1 ) 
		    $this->plots[$i]->posy *= 2 ;

		$this->plots[$i]->Stroke($this->img,1);

		if( $this->plots[$i]->posx > 1 ) 
		    $this->plots[$i]->posx /= 2 ;
		if( $this->plots[$i]->posy > 1 ) 
		    $this->plots[$i]->posy /= 2 ;
	    }

	    $indent = $this->doframe ? ($this->frame_weight + ($this->doshadow ? $this->shadow_width : 0 )) : 0 ;
	    $indent += $this->framebevel ? $this->framebeveldepth + 1 : 0 ;
	    $this->img->CopyCanvasH($oldimg,$this->img->img,$indent,$indent,$indent,$indent,
				    $w-2*$indent,$h-2*$indent,2*($w-$indent),2*($h-$indent));

	    $this->img->img = $oldimg ;
	    $this->img->width = $w ;
	    $this->img->height = $h ;

	    for($i=0; $i < $n; ++$i) {
		$this->plots[$i]->Stroke($this->img,2); // Stroke labels
		$this->plots[$i]->Legend($this);
	    }

	}
	else {

	    if( !$_csim ) {
		if( $this->background_image != "" ) {
		    $this->StrokeFrameBackground();		
		}
		else {
		    $this->StrokeFrame();		
		}
	    }

	    $this->StrokeIcons();

	    for($i=0; $i < $n; ++$i) {
		$this->plots[$i]->Stroke($this->img);
		$this->plots[$i]->Legend($this);
	    }
	}


	$this->legend->Stroke($this->img);
	$this->footer->Stroke($this->img);
	$this->StrokeTitles();

	// Stroke texts
	if( $this->texts != null ) {
	    $n = count($this->texts);
	    for($i=0; $i < $n; ++$i ) {
		$this->texts[$i]->Stroke($this->img);
	    }
	}

	if( !$_csim ) {	

	    if( _JPG_DEBUG ) {
		$this->DisplayCSIMAreas();
	    }

	    // Should we do any final image transformation
	    if( $this->iImgTrans ) {
		if( !class_exists('ImgTrans') ) {
		    require_once('jpgraph_imgtrans.php');
		    //JpGraphError::Raise('In order to use image transformation you must include the file jpgraph_imgtrans.php in your script.');
		}
	       
		$tform = new ImgTrans($this->img->img);
		$this->img->img = $tform->Skew3D($this->iImgTransHorizon,$this->iImgTransSkewDist,
						 $this->iImgTransDirection,$this->iImgTransHighQ,
						 $this->iImgTransMinSize,$this->iImgTransFillColor,
						 $this->iImgTransBorder);
	    }


	    // If the filename is given as the special "__handle"
	    // then the image handler is returned and the image is NOT
	    // streamed back
	    if( $aStrokeFileName == _IMG_HANDLER ) {
		return $this->img->img;
	    }
	    else {
		// Finally stream the generated picture					
		$this->cache->PutAndStream($this->img,$this->cache_name,$this->inline,
					   $aStrokeFileName);		
	    }
	}
    }
} // Class

/* EOF */
?>
