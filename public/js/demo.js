(function() {
    var a = {
        hostName: "",
        domain: "",
		tplName: "",
        locationHost: encodeURIComponent(window.location),
        isIE: navigator.userAgent.indexOf("MSIE") > 0,
        isIE6: navigator.userAgent.indexOf("MSIE 6") > 0,
        isIE7: navigator.userAgent.indexOf("MSIE 7") > 0,
        isIE8: navigator.userAgent.indexOf("MSIE 8") > 0,
        isIE9: navigator.userAgent.indexOf("MSIE 9") > 0,
        isFireFox: navigator.userAgent.indexOf("Firefox") > 0,
        isSafari: (navigator.userAgent.indexOf("Safari") > 0 && navigator.userAgent.indexOf("Chrome")) < 0,
        isChrome: (navigator.userAgent.indexOf("Safari") > 0 && navigator.userAgent.indexOf("Chrome")) > 0,
        isOpera: navigator.userAgent.indexOf("Opera") > -1,
        isSaAndChr: navigator.userAgent.indexOf("Safari") > 0,
        isMaxthon: navigator.userAgent.toLowerCase().indexOf("maxthon") > 0,
        isSogou: navigator.userAgent.toLowerCase().indexOf("mozilla") > -1 && navigator.userAgent.toLowerCase().indexOf("metasr") > -1,
        isOther: navigator.userAgent.toLowerCase().indexOf("mozilla") > -1 && navigator.userAgent.indexOf("MSIE") > 0,
        picWidth: 200,
        picHeight: 200,
        selected: [],
        init: function() {
			a.hostName = hostName;
			a.domain = domain;
			a.tplName = tplName;
			
            if (document.getElementById("imPingMaskDiv")) {
                return
            } else {
                var b = window.location.hostname;
                if (b.indexOf(a.hostName) > -1) {
                    alert("不可以在本站采图片哦！", "error");
                    return
                }
                var c = a.getImages();
                if (c.length == 0) {
                    alert("对不起，该页面没有找到合适的图片！");
                    return
                }
                a.addStyle();
                a.maskLayout();
                a.addLayout()
            }
        },
        maskLayout: function() {
            var c = document.createElement("div");
            var d = document.body.clientHeight;
            var b = document.body.clientWidth;
            a.isIE ? c.id = "imPingMaskDiv": c.setAttribute("id", "imPingMaskDiv");
            c.style.height = d + "px";
            c.style.width = b + "px";
            document.body.appendChild(c);
            a.updateDiv()
        },
        addStyle: function() {
            var e, b;
            if (a.isIE6 || a.isIE7) {
            	e = ".imSubmitInputOff{cursor:pointer;border:0;padding:0;background-image: url("+a.domain+"tpl/"+a.tplName+"/images/"+"addcc_ok0.gif);background-repeat:no-repeat ;visibility:visible;width:340px;height:52px;line-height:52px;margin:0 auto;vertical-align:bottom;}";
            	b = ".imSubmitInput{cursor:pointer;border:0;padding:0;margin:0;background-image: url("+a.domain+"tpl/"+a.tplName+"/images/"+"addcc_ok1.gif) ; background-repeat:no-repeat;visibility:visible;height: 52px;line-height:52px;margin: 0 auto;vertical-align: text-bottom;width: 340px;}";
            } else {
                e = "input[type='button'],.imSubmitInputOff{cursor:pointer;border:0;padding:0;background:url("+a.domain+"tpl/"+a.tplName+"/images/"+"addcc_ok0.gif) no-repeat;visibility:visible;width:340px;height:52px;line-height:52px;margin:0 auto;vertical-align:bottom;text-indent:-999em;}";
                b = "input[type='submit'],.imSubmitInput{cursor:pointer;border:0;padding:0;background: url("+a.domain+"tpl/"+a.tplName+"/images/"+"addcc_ok1.gif) no-repeat ; visibility:visible;height: 52px;line-height:52px;margin: 0 auto;vertical-align: text-bottom;width: 340px;text-indent:-999em;}";
            }
            var c = ["\nobject, embed{ visibility: hidden;}", "\n#imPingMaskDiv{position:absolute;left:0;top:0;opacity:.8;filter:alpha(opacity=80);z-index:1000000;background:#ffffff;}", "\n.defaultimg {background:#eee;width:192px;height:132px;padding-top:60px;display:block;}", "\n.pincontainer {position:absolute;top:0;left:0;z-index:1000002;width:100%;padding-top:80px;}", '\n.closewin {float:right;margin-right:10px;width:54px;height:54px;background: url("'+a.domain+"tpl/"+a.tplName+"/images/"+'pin.png") no-repeat 0 -775px;}', "\n.closewin:hover {background-position:-60px -775px;}", "\n.closewin:active {background-position:-120px -775px;}", "\n.divaddcc {left:0;padding-top:10px;height:70px; text-align:center;position:fixed;background:url("+a.domain+"tpl/"+a.tplName+"/images/"+"pintopbar.gif) repeat-x;width:100%;top:0;z-index:5;}", '\n.divaddcc {_position:absolute;_left:0px;_top:expression(eval(document.documentElement.scrollTop)+"px");}}', "\n.divaddcc .defaultimg {background:#ddd;width:192px;height:192px;display:block;}", "\n.divaddcc .checkimg,.divaddcc .checkedimg {cursor:pointer;vertical-align:bottom;display:inline-block;background:url("+a.domain+"tpl/"+a.tplName+"/images/"+"pinCheck.gif) no-repeat 10px 1px;padding-left:27px; height:15px; font-size:12px}", "\n.divaddcc .checkedimg {background-position:10px -30px;}", "\n.btadd,.btaddhover,.btadddisabled {background: url("+a.domain+"tpl/"+a.tplName+"/images/"+"addcc_03.gif) no-repeat;display:inline-block;width:340px;height:52px;margin:0 auto;vertical-align:bottom;}", "\n.btaddhover {background-position:0 -67px;}", "\n.btadddisabled {background-position:0 -132px;cursor:default;}", "\n a{cursor:pointer;}", "\n#imPingForm{text-align:center;}", "\n.getimglist {border-top: 1px solid #e7e7e7;border-left: 1px solid #e7e7e7;float:left;}", '\n.getimglist a {display: block;position: absolute;background:url("'+a.domain+"tpl/"+a.tplName+"/images/"+'pin.png") no-repeat 200px 0;width:192px;height: 192px;line-height:192px;left:4px;top:4px;_top:5px;z-index:2;}', "\n.getimglist a.selected {background-position:0 -193px;}", "\n.getimglist a.selected:hover {background-position:0 0;}", "\n.getimglist a.notselected:hover {background-position:0 -386px;}", "\n.getimglist a.btover {background-position:0 -579px;}", "\n.getimglist a span {position:absolute;top:80px;left:32px;width:132px;height:32px;display:block;cursor:pointer;}", "\n.getimglist div {border-right:1px solid #e7e7e7;border-bottom:1px solid #e7e7e7;padding:4px;float:left;position:relative;background:#fff;height:192px;width:192px;text-align:center;overflow:hidden;}", "\n.getimglist img {vertical-align:middle;}", '\n.getimglist em {font-style:normal;font-size:10px;font-family:Arial;position:absolute;bottom:10px;left:56px;text-align:center;width:85px;height:21px;z-index:1000;background:url("'+a.domain+"tpl/"+a.tplName+"/images/"+'pin_220x250.png") no-repeat;line-height:21px;z-index:1;}', b, e].join("");
            var d = document.createElement("style");
            d.type = "text/css";
            d.id="TUPU_STYLE";
            a.isIE ? (function() {
                d.media = "screen";
                d.styleSheet.cssText = c;
                document.getElementsByTagName("head")[0].appendChild(d)
            })() : (function() {
                d.innerHTML = c;
                document.body.appendChild(d)
            })()
        },
        getImages: function() {
        
            var k = document.images;
            var g = [];
            g = i(k);
            var d = document.getElementsByTagName("iframe");
           
            var h = [];
            try {
                for (var c = 0; c < d.length; c++) {
                    try {
                        h.push(d[c].contentWindow.document.images)
                    } catch(j) {}
                }
                for (var f = 0; f < h.length; f++) {
                	h[f].media_type=0;
                	h[f].video='';
                    g = g.concat(i(h[f]))
                }
                //添加视频
                _document=document;
                //alert(_document.location.href);
                 var url = _document.location.href,
   
        thumb = new Image,
        tudou_bigItemUrl = window.bigItemUrl || !1;
        if (url.indexOf("www.tudou.com") > 0 && tudou_bigItemUrl) {
            if (tudou_bigItemUrl.indexOf("w.jpg") == -1) {
                var image = tudou_bigItemUrl.split("/");
                tudou_bigItemUrl = tudou_bigItemUrl.replace(image[image.length - 1], "w.jpg")
            }
            thumb.src = tudou_bigItemUrl,
            thumb.width = 320,
            thumb.height = 240,
            thumb = encapsulateImage(thumb),
            thumb.container = _document.getElementById("playerObject"),
            thumb.video = "http://www.tudou.com/v/" + iid_code + "/v.swf",
            thumb.media_type = 1,
            g.push(thumb)
        } else if (url.indexOf("v.ku6.com") > 0)
        thumb.src = App.VideoInfo.data.data.bigpicpath,
        thumb.width = 480,
        thumb.height = 360,
        thumb = encapsulateImage(thumb),
        thumb.container = _document.getElementById("ku6player"),
        thumb.video = "http://player.ku6.com/refer/" + App.VideoInfo.id + "/v.swf",
        thumb.media_type = 1,
        g.push(thumb);
        else if (url.indexOf("v.youku.com") > 0) {
        
        	
            var json = eval("(" + httpGet("http://v.youku.com/player/getPlayList/VideoIDS/" + videoId2) + ")");
            thumb.src = json.data[0].logo,
            //alert(thumb.src),
            thumb.width = 448,
            thumb.height = 336,
            thumb = encapsulateImage(thumb),
            thumb.container = _document.getElementById("player"),
            thumb.video = "http://player.youku.com/player.php/sid/" + videoId2 + "/v.swf",
            thumb.media_type = 1,
            
            g.push(thumb)
        } else if (url.indexOf("v.ifeng.com") > 0) {
            var a_ifeng = url.split("/"),
            id = a_ifeng[a_ifeng.length - 1].split(".");
            id = id[0];
            var json = httpGet("http://v.ifeng.com/video_info_new/" + id[id.length - 2] + "/" + id[id.length - 2] + id[id.length - 1] + "/" + id + ".xml"),
            reg = /BigPosterUrl="(.+)" SmallPosterUrl/,
            a_ifeng = reg.exec(json);
            thumb.src = a_ifeng[1],
            thumb.width = 480,
            thumb.height = 360,
            thumb = encapsulateImage(thumb),
            thumb.container = _document.getElementById("playerDiv"),
            thumb.video = "http://v.ifeng.com/include/exterior.swf?guid=" + id + "&AutoPlay=false",
            thumb.media_type = 1,
            g.push(thumb)
        } else if (url.indexOf("www.56.com/u") > 0) {
            var reg = /v_(.+?)\.html/,
            a_56 = reg.exec(url);
            a_56 = a_56[1],
            
            thumb.src = "http://" + siteDomain + "/pins/create/video/56/" + a_56,
           
            thumb.width = 480,
            thumb.height = 405,
            thumb = encapsulateImage(thumb),
            thumb.container = _document.getElementById("myflashBox"),
            thumb.video = "http://player.56.com/v_" + a_56 + ".swf",
            thumb.media_type = 1,
            g.push(thumb)
        }
                //添加视频结束
                
            } catch(j) {}
            function i(e) {//进行筛选出 图片比例是宽度大于200 高度大于 200的图片
                var n = e;
                var m = [];
                for (var l = 0,
                b = n.length; l < b; l++) {
                    if (parseInt(n[l].width) > 200 && parseInt(n[l].height) > 200) {
                        m.push(n[l])
                    }
                }
                return m
            }
            return g
        },
        addImages: function() {
            var k = a.getImages();
            var c = "";
            var g = [];
            for (var d = 0,
            b = k.length; d < b; d++) {
                var j = [];
                j[0] = k[d].width * k[d].height;
                j[1] = k[d];
                g.push(j)
            }
            g.sort(function(h, i) {
                return h[0] - i[0]
            });
            var k = g;
            for (var b = k.length; b--;) {
                if (parseInt(k[b][1].width) > parseInt(k[b][1].height)) {
                    var e = Math.floor(k[b][1].height / (k[b][1].width / 192));
                    var f = Math.floor((200 - e) / 2);
                    c += ("<div class='imImg'><img width='192' style='margin-top:" + f + "px' src='" + k[b][1].src + "' alt='" + k[b][1].video + "'/><a class='notselected' href='javascript:void(0);'><span></span></a><em>" + k[b][1].width + "x" + k[b][1].height + "</em></div>")
                } else {
                    c += ("<div class='imImg'><img height='192' src='" + k[b][1].src + "'  alt='" + k[b][1].video + "'/><a class='notselected' href='javascript:void(0);'><span></span></a><em>" + k[b][1].width + "x" + k[b][1].height + "</em></div>")
                }
            }
            return c
        },
        getObj: function(c) {
            if (a.isIE) {
                var e = [];
                for (var f = 0,
                d = c.length; f < d; f++) {
                    if (c[f] != document.getElementById("imLogo")) {
                        e.push(c[f])
                    }
                }
                objDiv = e
            } else {
                var e = [];
                for (var b = 0,
                d = c.length; b < d; b++) {
                    if (c[b].nodeType == 1 && c[b] != document.getElementById("imLogo")) {
                        e.push(c[b])
                    }
                }
                objDiv = e
            }
            return objDiv
        },
        addLayout: function() {
            var l = document.createElement("DIV");
            a.isIE ? (function() {
                l.id = "imMain";
                l.className = "pincontainer"
            })() : (function() {
                l.setAttribute("id", "imMain");
                l.setAttribute("class", "pincontainer")
            })();
            document.body.appendChild(l);
            var c = document.createElement("div");
            a.isIE ? (function() {
                c.id = "imTitle";
                c.className = "divaddcc"
            })() : (function() {
                c.setAttribute("id", "imTitle");
                c.setAttribute("class", "divaddcc")
            })();
            l.appendChild(c);
            var d = ['<form id="imPingForm" name="imPingForm" >', '<a id="imClosePing" class="closewin" href="javascript:void(0);"></a>', '<input id="button" type="button" value="" class="imSubmitInputOff" readonly="readonly" />', '<span id="imCheckAll" class="checkimg">选择全部</span>', "</form>"].join("");
            c.innerHTML = d;
            var e = document.createElement("div");
            a.isIE ? (function() {
                e.className = "getimglist";
                e.id = "imContent"
            })() : (function() {
                e.setAttribute("id", "imContent");
                e.setAttribute("class", "getimglist")
            })();
            l.appendChild(e);
            e.innerHTML = a.addImages();
            var m = document.createElement("div");
            m.setAttribute("id", "imLogo");
            e.insertBefore(m, e.firstChild);
            m.innerHTML = '<span class="defaultimg"><img src="'+a.domain+"tpl/"+a.tplName+"/images/"+'logo_pin.gif" /></span>';
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
            var b = document.getElementById("imContent").childNodes;
            var n = a.getObj(b);
            for (var g = 0,
            h = n.length; g < h; g++) {
                n[g].getElementsByTagName("a")[0].onclick = function() {
                    j(this);
                    return false
                }
            }
            document.getElementById("imCheckAll").onclick = function() {
                k(this)
            };
            document.getElementById("imClosePing").onclick = function() {
            	f()
            };
            document.getElementById("button").onclick = function() {
            	f()
            };
            function j(s) {
                if (s.className == "notselected") {
                    s.className = "selected";
                    s.parentNode.setAttribute("selected", "true")
                } else {
                    s.className = "notselected";
                    s.parentNode.setAttribute("selected", "false")
                }
                var q = [];
                var v = [];
                for (var i = 0; i < n.length; i++) {
                    if (n[i].getAttribute("selected") == "true") {
                        q.push(n[i].getElementsByTagName("img")[0].src);
                       // alert(n[i].getElementsByTagName("img")[0].alt);
                        v.push(n[i].getElementsByTagName("img")[0].alt);
                    }
                }
                var r = a.domain + "/collection.php?action=photo&imgUrl=" + q.join(",") + "&videoUrl="+v.join(",")+"&pageUrl=" + a.locationHost + "&cookie='" + a.getCookie() + "'";
                if (q.length == 0) {
                    var o = ['<form id="imPingForm" name="imPingForm" >', '<a id="imClosePing" class="closewin" href="javascript:void(0);"></a>', '<input id="button" type="button" value="" class="imSubmitInputOff" readonly="readonly" />', '<span id="imCheckAll" class="checkedimg">选择全部</span>', "</form>"].join("");
                    document.getElementById("imTitle").innerHTML = o
                } else {
                    if (a.isSogou || a.isIE9 || a.isMaxthon) {
                        var o = ['<form id="imPingForm" name="imPingForm" onsubmit="javascript:function(){window.open(\'\',\'imPingTool\',\'width=790,height=490,scrollbars=yes\');}()"  action="' + r + '" method="post" target="imPingTool">', '<a id="imClosePing" class="closewin" href="javascript:void(0);"></a>', '<input id="imSubmit" type="submit" value="" class="imSubmitInput" />', '<span id="imCheckAll" class="checkimg">选择全部</span>', "</form>"].join("");
                        document.getElementById("imTitle").innerHTML = o
                    } else {
                        var p = document.createElement("form");
                        p.onsubmit = function() {
                            return im20_submitFunc()
                        };
                        p.setAttribute("id", "imPingForm");
                        p.setAttribute("name", "imPingForm");
                        p.setAttribute("action", r);
                        p.setAttribute("method", "post");
                        p.setAttribute("target", "imPingTool");
                        document.getElementById("imTitle").innerHTML = "";
                        document.getElementById("imTitle").appendChild(p);
                        var o = ['<a id="imClosePing" class="closewin" href="javascript:void(0);"></a>', '<input id="imSubmit" type="submit" value="" class="imSubmitInput" />', '<span id="imCheckAll" class="checkimg" >选择全部</span>'].join("");
                        p.innerHTML = o
                    }
                }
                document.getElementById("imCheckAll").onclick = function() {
                    k(this)
                };
                document.getElementById("imClosePing").onclick = function() {
                	f()
                };
                document.getElementById("button").onclick = function() {
                	f()
                };
            }
            function f() {
                document.body.removeChild(document.getElementById("imPingMaskDiv"));
                document.body.removeChild(document.getElementById("imMain"));
                document.body.removeChild(document.getElementById("TUPU_STYLE"))
            }
            function k(r) {
                var v;
                a.isIE ? v = r.className: v = r.getAttribute("class");
                if (v == "checkedimg") {
                    a.isIE ? r.className = "checkimg": r.setAttribute("class", "checkimg");
                    var o = document.getElementById("imContent").children;
                    var w = a.getObj(o);
                    for (var s = 0,
                    t = w.length; s < t; s++) {
                        w[s].setAttribute("selected", "false");
                        a.isIE ?
                        function() {
                            w[s].getElementsByTagName("a")[0].className = "notselected"
                        } () : function() {
                            w[s].getElementsByTagName("a")[0].setAttribute("class", "notselected")
                        } ()
                    }
                    var q = ['<form id="imPingForm" name="imPingForm" >', '<a id="imClosePing" class="closewin" href="javascript:void(0);"></a>', '<input id="button" type="button" value="" class="imSubmitInputOff" readonly="readonly" />', '<span id="imCheckAll" class="checkimg">选择全部</span>', "</form>"].join("");
                    document.getElementById("imTitle").innerHTML = q
                } else {
                    a.isIE ? r.className = "checkedimg": r.setAttribute("class", "checkedimg");
                    var o = document.getElementById("imContent").children;
                    var w = a.getObj(o);
                    var videos=[];
                    a.selected.length = 0;
                    for (var s = 0,
                    t = w.length; s < t; s++) {
                        w[s].setAttribute("selected", "true");
                        a.isIE ? w[s].getElementsByTagName("a")[0].className = "selected": w[s].getElementsByTagName("a")[0].setAttribute("class", "selected");
                        a.selected.push(w[s].getElementsByTagName("img")[0].src);
                        videos.push(w[s].getElementsByTagName("img")[0].alt);
                    }
                    var p = a.domain + "/collection.php?action=photo&imgUrl=" + a.selected.join(",") + "&videoUrl="+videos.join(",")+"&pageUrl=" + a.locationHost + "&cookie='" + a.getCookie() + "'";
                    if (a.isSogou || a.isIE9 || a.isMaxthon) {
                        var q = ['<form id="imPingForm" name="imPingForm" onsubmit="javascript:function(){window.open(\'\',\'imPingTool\',\'width=980,height=490,scrollbars=yes\');}()"  action="' + p + '" method="post" target="imPingTool">', '<a id="imClosePing" class="closewin" href="javascript:void(0);"></a>', '<input id="imSubmit" type="submit" value="" class="imSubmitInput" />', '<span id="imCheckAll" class="checkedimg">选择全部</span>', "</form>"].join("");
                        document.getElementById("imTitle").innerHTML = q
                    } else {
                        var u = document.createElement("form");
                        u.onsubmit = function() {
                            return im20_submitFunc()
                        };
                        u.setAttribute("id", "imPingForm");
                        u.setAttribute("name", "imPingForm");
                        u.setAttribute("action", p);
                        u.setAttribute("method", "post");
                        u.setAttribute("target", "imPingTool");
                        document.getElementById("imTitle").innerHTML = "";
                        document.getElementById("imTitle").appendChild(u);
                        var q = ['<a id="imClosePing" class="closewin" href="javascript:void(0);"></a>', '<input id="imSubmit" type="submit" value="" class="imSubmitInput" />', '<span id="imCheckAll" class="checkedimg" >选择全部</span>'].join("");
                        u.innerHTML = q
                    }
                }
                document.getElementById("imCheckAll").onclick = function() {
                    k(this)
                };
                document.getElementById("imClosePing").onclick = function() {
                	f()
                };
                document.getElementById("button").onclick = function() {
                	f()
                };
            }
        },
        closePinWin: function() {
            document.body.removeChild(document.getElementById("imPingMaskDiv"));
            document.body.removeChild(document.getElementById("imMain"));
            document.body.removeChild(document.getElementById("TUPU_STYLE"));
            document.getElementById("imPingForm").onsubmit()
        },
        updateDiv: function() {
            var f = document.getElementsByTagName("body")[0].children;
            var e = 0;
            for (var d = 0,
            b = f.length; d < b; d++) {
                if (f[d].nodeName == "DIV" && f[d].style.display != "none" && f[d].getAttribute("id") != "imPingMaskDiv" && f[d].getAttribute("id") != "imMain") {
                    e += f[d].offsetHeight
                }
            }
            var g = Math.max(document.documentElement.clientHeight, document.body.clientHeight);
            var c = Math.max(e, g);
            document.getElementById("imPingMaskDiv") ? document.getElementById("imPingMaskDiv").style.height = c + "px": (function() {
                return
            })()
        },
        getCookie: function() {
            return document.cookie;
            function b(g) {
                var f = documents.cookie.indexOf(";", g);
                if (f == -1) {
                    f = documents.cookie.length
                }
                return unescape(documents.cookie.substring(g, f))
            }
            function e(g, m) {
                var h = new Date();
                var l = e.arguments;
                var j = e.arguments.length;
                var i = (j > 2) ? l[2] : null;
                var n = (j > 3) ? l[3] : null;
                var k = (j > 4) ? l[4] : null;
                var f = (j > 5) ? l[5] : false;
                if (i != null) {
                    h.setTime(h.getTime() + (i * 1000))
                }
                documents.cookie = g + "=" + escape(m) + ((i == null) ? "": ("; expires=" + h.toGMTString())) + ((n == null) ? "": ("; path=" + n)) + ((k == null) ? "": ("; domain=" + k)) + ((f == true) ? "; secure": "")
            }
            function d(f) {
                var h = new Date();
                h.setTime(h.getTime() - 1);
                var g = c(f);
                documents.cookie = f + "=" + g + "; expires=" + h.toGMTString()
            }
            function c(k) {
                var g = k + "=";
                var m = g.length;
                var f = documents.cookie.length;
                var l = 0;
                while (l < f) {
                    var h = l + m;
                    if (documents.cookie.substring(l, h) == g) {
                        return b(h)
                    }
                    l = documents.cookie.indexOf(" ", l) + 1;
                    if (l == 0) {
                        break
                    }
                }
                return null
            }
        }
    };
    document[global]=a;
    a.init();
    window.onresize = a.updateDiv;
    window.onscroll = a.updateDiv
})();
function im20_submitFunc() {
    window.open("imPingTool", "imPingTool", "width=980,height=490,scrollbars=yes");
    setTimeout(function() {
        im20_closePing()
    },
    1000);
    return true
}
 function httpGet(vido_url) {
 	
        var b = null;
        return b = new XMLHttpRequest,
        b.open("GET", vido_url, !1),
        b.send(null),
        b.responseText
    }
    function encapsulateImage(a) {
        var b = new Image;
        b.src = a.src;
        var c = {
            width: a.width,
            height: a.height,
            src: a.src,
            img: a,
            alt: "",
            img2: b
        };
        return c
    }
function im20_closePing() {
    document.body.removeChild(document.getElementById("imPingMaskDiv"));
    document.body.removeChild(document.getElementById("imMain"));
    document.body.removeChild(document.getElementById("TUPU_STYLE"))
};