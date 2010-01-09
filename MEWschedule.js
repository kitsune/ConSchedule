/*
 *		Copyright © 2010 Mark Harviston <infinull@gmail.com>
 *		ALL RIGHTS RESERVED
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 *
 */

function center_stuff(){
	var divs = document.getElementsByTagName('div');
	for(var i = 0; i < divs.length; i+=1){
		if(divs[i].className == 'event_container'){
			//fix height:
			divs[i].style.height = '' + divs[i].parentNode.offsetHeight + 'px';

			//remove start/end from half-hour
		} else if(divs[i].className == 'event'){
			var to_set = Math.round(divs[i].offsetHeight/2);
			divs[i].style.marginTop = '-'+ to_set + 'px';
		}
	}

}
function col_width_set(){
	var the_width = document.body.offsetWidth;
	var num_rooms = 7.0;
	var timecolpercent = .07;
	var othercolpercent = (1.0 - timecolpercent)/num_rooms; 
	var timecolwidth = Math.round(the_width * timecolpercent) + 'px';
	var othercolwidth = Math.round(the_width * othercolpercent) + 'px';

	var theads=document.getElementsByTagName('thead');
	for(var i = 0; i < theads.length; i+=1){
		theads[i].style.width = the_width + 'px';
		ths = theads[i].children[0].children;
		ths[0].style.width = timecolwidth;
		for(var j=1; j < ths.length; j+=1){
			ths[j].style.width = othercolwidth;
		}
	}

	var tables=document.getElementsByTagName('table');
	for(var i = 0; i < tables.length; i+=1){
		tables[i].style.width = the_width + 'px';
		//table->tbody->tr[0]->
		tables[i].children[1].children[0].children[0].style.width = timecolwidth;
		for(var j=1; j < tables[i].children[1].children[0].children.length; j+=1){
			tables[i].children[1].children[0].children[j].style.width = othercolwidth;
		}
	}
}

window.onload = function(){
	//col_width_set();
	center_stuff();
}

window.onresize = function(){
	//col_width_set();
}
