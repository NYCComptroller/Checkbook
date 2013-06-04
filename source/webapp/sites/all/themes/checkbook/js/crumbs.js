/*
Copyright Justin Whitford 2006.
  http://www.whitford.id.au/
Perpetual, non-exclusive license to use this code is granted
on the condition that this notice is left in tact.
*/

var delim = '|';
var trailLength = 5;
var chunks;
var DAY = 24 * 60 * 60 * 1000;

function doCrumbs(){
  if(cookieTest('xxx')){
    crumbList = new CrumbList();
    if(getCookie('trailLinks')){
      var staleLinkCrumbs = getCookie('trailLinks').split(delim);
      var staleTextCrumbs = getCookie('trailText').split(delim);
      var startPos=
        (staleTextCrumbs.length<trailLength ||
        document.location==staleLinkCrumbs[staleLinkCrumbs.length-1])
        ?0:1;
      for(i=startPos;i<staleLinkCrumbs.length;i++){
        crumbList.add(staleLinkCrumbs[i],staleTextCrumbs[i]);
      }
    }
    if(document.location!=crumbList.links[crumbList.links.length-1]){
      crumbList.add(document.location,document.title);
    }
    setCookie('trailLinks',crumbList.links.join(delim),1);
    setCookie('trailText',crumbList.text.join(delim),1);
    crumbList.output();
  }
}


function CrumbList(){
  this.links=new Array();
  this.text=new Array();
  this.add = crumbListAdd;
  this.output = crumbListShow;
}
  function crumbListAdd(href,text){
    this.links[this.links.length]=href;
    this.text[this.text.length]=text;
  }
  function crumbListShow(){
    for(var i in this.links){
      if(i==this.links.length-1){
        document.write( ((i==0)?"":" | ") + this.text[i] );
      }else{
        document.write(
          ((i==0)?"":" | ")
          +"<a href='" + this.links[i] + "'>"
          + this.text[i] + "</a>"
        );
      }
    }
  }


function cookieTest(name){
  try{
    setCookie(name,'true',1);
    chunks = document.cookie.split("; ");
    return (getCookie(name)=='true');
  }catch(e){
    return false;
  }
}

function getCookie(name) {
  var returnVal = null;
  for (var i in chunks) {
    var chunk = chunks[i].split("=");
    returnVal = (chunk[0] == name)
      ?unescape(chunk[1])
      :returnVal;
  }
  return returnVal;
}

function setCookie(name, value, days) {
  if (value != null && value != "" && days > 0){
    var expiry=
      new Date(new Date().getTime() + days * DAY);
    document.cookie=
      name +"="+ escape(value) +"; expires="
      + expiry.toGMTString();
    chunks = document.cookie.split("; ");
  }
}