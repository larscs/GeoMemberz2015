
function getSortKey(){
    //default
    var sortval = 'sortkey=[[0,0]]&amp;';
    if(document.getElementById('col0').className=='header headerSortDown'){sortval = 'sortkey=[[0,0]]&';}
    if(document.getElementById('col0').className=='header headerSortUp')  {sortval = 'sortkey=[[0,1]]&';}
    if(document.getElementById('col1').className=='header headerSortDown'){sortval = 'sortkey=[[1,0]]&';}
    if(document.getElementById('col1').className=='header headerSortUp')  {sortval = 'sortkey=[[1,1]]&';}
    if(document.getElementById('col2').className=='header headerSortDown'){sortval = 'sortkey=[[2,0]]&';}
    if(document.getElementById('col2').className=='header headerSortUp')  {sortval = 'sortkey=[[2,1]]&';}
    if(document.getElementById('col3').className=='header headerSortDown'){sortval = 'sortkey=[[3,0]]&';}
    if(document.getElementById('col3').className=='header headerSortUp')  {sortval = 'sortkey=[[3,1]]&';}
    if(document.getElementById('col4').className=='header headerSortDown'){sortval = 'sortkey=[[4,0]]&';}
    if(document.getElementById('col4').className=='header headerSortUp')  {sortval = 'sortkey=[[4,1]]&';}
    if(document.getElementById('col5').className=='header headerSortDown'){sortval = 'sortkey=[[5,0]]&';}
    if(document.getElementById('col5').className=='header headerSortUp')  {sortval = 'sortkey=[[5,1]]&';}
    if(document.getElementById('col6').className=='header headerSortDown'){sortval = 'sortkey=[[6,0]]&';}
    if(document.getElementById('col6').className=='header headerSortUp')  {sortval = 'sortkey=[[6,1]]&';}
    return sortval;
}

function getSortKeyPayments(){
    //default
    var sortval = 'sortkey=[[0,0]]&amp;';
    if(document.getElementById('col0').className=='header headerSortDown'){sortval = '[[0,0]]';}
    if(document.getElementById('col0').className=='header headerSortUp')  {sortval = '[[0,1]]';}
    if(document.getElementById('col1').className=='header headerSortDown'){sortval = '[[1,0]]';}
    if(document.getElementById('col1').className=='header headerSortUp')  {sortval = '[[1,1]]';}
    if(document.getElementById('col2').className=='header headerSortDown'){sortval = '[[2,0]]';}
    if(document.getElementById('col2').className=='header headerSortUp')  {sortval = '[[2,1]]';}
    if(document.getElementById('col3').className=='header headerSortDown'){sortval = '[[3,0]]';}
    if(document.getElementById('col3').className=='header headerSortUp')  {sortval = '[[3,1]]';}
    if(document.getElementById('col4').className=='header headerSortDown'){sortval = '[[4,0]]';}
    if(document.getElementById('col4').className=='header headerSortUp')  {sortval = '[[4,1]]';}
    if(document.getElementById('col5').className=='header headerSortDown'){sortval = '[[5,0]]';}
    if(document.getElementById('col5').className=='header headerSortUp')  {sortval = '[[5,1]]';}
    return sortval;
}
function payStep1(id) {
    document.getElementById('scrollpos'+id).value=self.pageYOffset;
    document.getElementById('formdate'+id).style.display='block';
    document.getElementById('formamount'+id).style.display='block';
    document.getElementById('submit'+id).value='OK';
    
    
    document.getElementById('submit'+id).setAttribute("onclick","payStep2('"+id+"');return false;");
}
function payStep2(id) {
    document.getElementById('date'+id).value = document.getElementById('formdate'+id).value;
    document.getElementById('amount'+id).value = document.getElementById('formamount'+id).value;
    document.getElementById('sortkey'+id).value = getSortKey();
    document.getElementById('member"+id+"').submit()
}