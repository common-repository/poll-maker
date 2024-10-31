// let material_btn = (function() {
//     let buttons = $(".choosing label");

//     function addLine() {
//         for (let i = 0; i < buttons.length - 1; i++) {
//             buttons.eq(i).append("<span class='line'></span>");
//         };
//     }

//     function addCircle() {
//         for (let i = 0; i < buttons.length; i++) {
//             buttons.eq(i).append("<span class='circle hidden'></span>");
//         };
//     }

//     function addText() {
//         for (let i = 0; i < buttons.length; i++) {
//             let text = buttons.eq(i).text();
//             buttons.eq(i).text("");
//             buttons.eq(i).append("<span class='text'>" + text + "</span>");
//         };
//     }

//     let animation = false;

//     $(".choosing label").click(function(event) {
//         $(".choosing label").each(function() {
//             $(this).removeClass('active-answer');
//         })
//         if (!animation) {
//             animation = true;
//             let btn = $(this)
//             btn.find(".circle")
//                 .removeClass("hidden")
//                 .css("left", event.offsetX)
//                 .css("top", event.offsetY);
//             setTimeout(function() {
//                 btn.find(".circle").addClass("active");
//             }, 10);
//             setTimeout(function() {
//                 btn.find(".circle").addClass("hidden");
//                 btn.addClass('active-answer');
//             }, 400);
//             setTimeout(function() {
//                 btn.find(".circle")
//                     .removeClass("active")
//                     .attr("style", "");
//                 animation = false;
//             }, 810);
//         }

//     })

//     return {
//         init: function() {
//             addText();
//             addLine();
//             addCircle();
//         }
//     }
// })();
// material_btn.init();