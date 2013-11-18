$(function(){
        var scrtime;
         $("#wb_conts").hover(function(){
                clearInterval(scrtime);

        },function(){
        scrtime = setInterval(function(){
                var $ul = $("#wb_conts ul");
                var liHeight = $ul.find(".bm_content:last").height();
                $ul.animate({marginTop : liHeight+17 +"px"},1000,function(){

                $ul.find(".bm_content:last").prependTo($ul)
                $ul.find(".bm_content:first").hide();
                $ul.css({marginTop:0});
                $ul.find(".bm_content:first").fadeIn(1000);
                });
        },3000);
        }).trigger("mouseleave");
});