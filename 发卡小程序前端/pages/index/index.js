var app = getApp(), utils = require("../../utils/util");
let interstitialAd = null
var e = null;

Page({

    data: {
        data: [],
        classArray: [],
        allClassArray: [],
        classIndex: 0,
        shareShow: !1,
        cid: "",
        searchKeyword: "",
        videoWatchInfo: {
            watched_times: 0,
            required_times: 1,
            is_completed: false,
            remaining_times: 1,
            daily_watch_count: 0,
            daily_limit: 10,
            daily_remaining: 10,
            interval_watch_count: 0,
            interval_limit: 3,
            interval_minutes: 30
        }
    },

    onLoad: function onLoad() {
this. interstitialAd();
      try {
        const res = wx.getSystemInfoSync()
        console.log(res.platform)
        if(res.platform=='windows'||res.platform=='mac'){
            wx.showModal({
             title: '温馨提示!',
             content:'请使用手机端登录',
             showCancel:false,
             success(res) {
              wx.exitMiniProgram()
             }
            });
            return;
           }
      } catch (e) {
      }


       this.getClassDatas();
      
      },
      interstitialAd:function(){ 
      if (wx.createInterstitialAd) {
        interstitialAd = wx.createInterstitialAd({
          adUnitId: "你的广告ID"
        })
        interstitialAd.onLoad(() => {})
        interstitialAd.onError((err) => {})
        interstitialAd.onClose(() => {})
      }
      if (interstitialAd) {
        interstitialAd.show().catch((err) => {})
      }
    },

    getClassDatas: function getClassDatas() {
        var that = this;
        wx.login({
            success: function success(code) {
                utils.getRequest("/Api/classDatas.php?code=" + code.code).then(function(res) {
                    console.log(res);
                    if (res.data.code == 200) {
                        that.setData({
                            data: res.data.data,
                            classArray: res.data.class,
                            allClassArray: res.data.class,
                            cid: res.data.class[0].cid,
                            videoWatchInfo: {
                                daily_watch_count: res.data.data.daily_watch_count || 0,
                                daily_limit: res.data.data.daily_video_limit || 10,
                                daily_remaining: res.data.data.daily_remaining || 10,
                                interval_watch_count: 0,
                                interval_limit: res.data.data.interval_video_limit || 3,
                                interval_minutes: res.data.data.interval_minutes || 30
                            }
                        });
                        wx.setNavigationBarTitle({
                            title: res.data.data.xcx_name
                        });
                    } else {
                        wx.showModal({
                            title: "提示",
                            content: res.data.msg,
                            showCancel: !1,
                            confirmText: "重试",
                            success: function success() {
                                that.getClassDatas();
                            }
                        });
                    }
                }).catch(function(res) {
                    console.log(res);
                    wx.showModal({
                        title: "提示",
                        content: "网络请求超时",
                        confirmText: "重试",
                        success: function success() {
                            that.getClassDatas();
                        }
                    });
                });
            }
        });
    },
    
    onSearchInput: function(e) {
        var keyword = e.detail.value.toLowerCase();
        var allClasses = this.data.allClassArray;
        
        if (keyword === '') {
            this.setData({
                classArray: allClasses,
                searchKeyword: '',
                classIndex: 0,
                cid: allClasses[0].cid
            });
        } else {
            var filteredClasses = allClasses.filter(function(item) {
                return item.name.toLowerCase().indexOf(keyword) !== -1;
            });
            
            if (filteredClasses.length > 0) {
                this.setData({
                    classArray: filteredClasses,
                    searchKeyword: keyword,
                    classIndex: 0,
                    cid: filteredClasses[0].cid
                });
            } else {
                this.setData({
                    classArray: allClasses,
                    searchKeyword: keyword
                });
                wx.showToast({
                    title: '未找到匹配分类',
                    icon: 'none',
                    duration: 2000
                });
            }
        }
    },
    
    classChange: function classChange(e) {
        var cid = this.data.classArray[e.detail.value].cid;
        console.log("选的是", e.detail.value);
        console.log("选的名称", this.data.classArray[e.detail.value].name);
        console.log("选的名称ID", cid);
        this.setData({
            classIndex: e.detail.value,
            cid: cid
        });
    },
    
    onProductTap: function(e) {
        console.log("=== 点击商品卡片 ===");
        var index = e.currentTarget.dataset.index;
        console.log("商品索引:", index);
        
        // 跳转到详情页
        wx.navigateTo({
            url: '/pages/detail/detail?index=' + index
        });
    },
    receive: function receive() {
        var that = this;
        var currentClass = that.data.classArray[that.data.classIndex];
        console.log("当前分类视频次数：", currentClass.video_times);
        console.log("当前分类视频次数类型：", typeof currentClass.video_times);

        var videoTimes = parseInt(currentClass.video_times);
        console.log("转换后的视频次数：", videoTimes);
        console.log("转换后的类型：", typeof videoTimes);

        if (videoTimes === 0) {
            console.log("视频次数为0，直接获取卡密");
            that.ok();
            return;
        } else if (videoTimes > 1) {
            if (currentClass.watched_times < videoTimes) {
                console.log("需要观看更多视频");
                that.watchVideo();
            } else {
                console.log("已完成多次观看，获取卡密");
                that.ok();
            }
        } else {
            console.log("需要观看一次视频");
            that.watchVideo();
        }
    },

    watchVideo: function() {
        var that = this;
        var currentClass = that.data.classArray[that.data.classIndex];

        if (!e) {
            e = wx.createRewardedVideoAd({
                adUnitId: that.data.data.adVideoId
            });
            e.onError(function(res) {
                wx.showModal({
                    title: "提示",
                    content: "视频广告加载失败",
                    showCancel: !1,
                    success: function success(res) {}
                });
            });
        }

        e.show().catch(function() {
            e.load().then(function() {
                e.show();
            });
        });

        e.onClose(function(res) {
            if (res && res.isEnded || void 0 === res) {
                wx.login({
                    success: function success(code) {
                        utils.getRequest("/Api/recordVideoWatch.php?code=" + code.code + "&cid=" + that.data.cid).then(function(res) {
                            console.log(res);
                            if (res.data.code == 200) {
                                var classArray = that.data.classArray;
                                classArray[that.data.classIndex].watched_times = res.data.data.watched_times;
                                classArray[that.data.classIndex].video_status = res.data.data.is_completed ? 1 : 0;

                                that.setData({
                                    classArray: classArray,
                                    videoWatchInfo: res.data.data
                                });

                                wx.showModal({
                                    title: "观看进度",
                                    content: "已观看 " + res.data.data.watched_times + "/" + res.data.data.required_times + " 次视频广告" +
                                             (res.data.data.is_completed ? "，达到领取条件！" : "，还需观看 " + res.data.data.remaining_times + " 次"),
                                    showCancel: !1,
                                    success: function success() {
                                        if (res.data.data.is_completed) {
                                            if (that.data.data.examine == 3) {
                                                that.setData({
                                                    shareShow: !0
                                                });
                                            } else if (that.data.data.examine == 2 || currentClass.video_times == 1) {
                                                that.ok();
                                            }
                                        }
                                    }
                                });
                            } else {
                                wx.showModal({
                                    title: "提示",
                                    content: res.data.msg,
                                    showCancel: !1
                                });
                            }
                        }).catch(function(err) {
                            console.log(err);
                            wx.showModal({
                                title: "提示",
                                content: "网络请求超时",
                                showCancel: !1
                            });
                        });
                    }
                });
            } else {
                wx.showModal({
                    title: "提示",
                    content: that.data.data.adVideoTip != "" ? that.data.data.adVideoTip : "视频未完整观看，无法获取奖励",
                    showCancel: !1
                });
            }

            e.offClose();
        });
    },
    
    popupShow: function popupShow() {
        this.setData({
            shareShow: !1
        });
    },
    ok: function ok() {
        var that = this;
        this.setData({
            shareShow: false
        });

        wx.login({
            success: function success(code) {
                utils.getRequest("/Api/getCarmel.php?code=" + code.code + "&cid=" + that.data.cid).then(function(res) {
                    console.log(res);
                    if (res.data.code == 200) {
                        wx.showModal({
                            title: "领取成功",
                            content: "卡密：" + res.data.carmel + "\n\n卡密已复制到剪贴板",
                            showCancel: false,
                            success: function() {
                                wx.setClipboardData({
                                    data: res.data.carmel,
                                    success: function() {
                                        wx.showToast({
                                            title: "复制成功",
                                            icon: "success",
                                            duration: 2000
                                        });
                                    }
                                });
                                that.getClassDatas();
                            }
                        });
                    } else {
                        wx.showModal({
                            title: "提示",
                            content: res.data.msg,
                            showCancel: false
                        });
                    }
                }).catch(function(err) {
                    console.log(err);
                    wx.showModal({
                        title: "提示",
                        content: "网络请求超时",
                        showCancel: false
                    });
                });
            }
        });
    },
    onShareAppMessage: function onShareAppMessage() {
        return {
            title: this.data.data.shareTitle ? this.data.data.shareTitle : "这是个神奇的东西",
            path: "/pages/index/index",
            imageUrl: this.data.data.shareImg ? this.data.data.shareImg : "../../img/share.jpg",
            success: function success(t) {
                wx.showToast({
                    title: "分享成功",
                    icon: "success",
                    duration: 2e3
                });
            },
            fail: function fail(t) {
                wx.showToast({
                    title: "分享失败",
                    icon: "none",
                    duration: 2e3
                });
            }
        };
    }
});
