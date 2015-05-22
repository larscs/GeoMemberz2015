  function initStuff() {
    $('[data-toggle="popover"]').popover({trigger: 'focus', 'placement': 'top'});
  }
  function closeAll() {
    // Find all elements "details" and display:none them.
    $("tr[id^='details']").css("display","none");
    $("span[id^='span']").attr("class","glyphicon glyphicon-chevron-right");
    
  }
  function toggleDisplay(memnum) {
    // Now the table might have been sorted, and the "spacer" and "details" will not be at their correct positions.
    // remove them from the DOM an insert them in the correct place after the "row".
    $('#spacer'+memnum).insertAfter('#row'+memnum);
    $('#details'+memnum).insertAfter('#spacer'+memnum);
    
    myelem = document.getElementById('details'+memnum);
    myicon = document.getElementById('span'+memnum);
    if(myelem.style.display=="table-row") {
        myelem.style.display="none";
        myicon.className = "glyphicon glyphicon-chevron-right";
    } else {
        myelem.style.display="table-row";
        myicon.className = "glyphicon glyphicon-chevron-down";
    }

  }