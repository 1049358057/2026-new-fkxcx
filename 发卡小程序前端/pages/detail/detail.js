var app = getApp(), utils = require("../../utils/util");
var rewardedVideoAd = null;
var returnTimer = null;
Page({
    data: {
        productName: '',
        introduce: '',
        videoTimes: 0,
        watchedTimes: 0,
        remainingTimes: 0,
        cid: '',
        shareShow: false,
        shareTip: '',
        adVideoId: '',
        examine: 0
    },

    onLoad: function(options) {
        console.log("详情页参数:", options);
        this._unloaded = false;
        
        var that = this;
        var index = options.index || 0;
        
        // 从首页获取数据
        var pages = getCurrentPages();
        var prevPage = pages[pages.length - 2];
        
        if (prevPage && prevPage.data.classArray) {
            var product = prevPage.data.classArray[index];
            var globalData = prevPage.data.data;
            
            console.log("商品数据:", product);
            console.log("全局数据:", globalData);
            
            var videoTimes = parseInt(product.video_times) || 0;
            var watchedTimes = parseInt(product.watched_times) || 0;
            var remainingTimes = Math.max(0, videoTimes - watchedTimes);
            
            that.setData({
                productName: product.name,
                introduce: product.introduce || '暂无介绍',
                videoTimes: videoTimes,
                watchedTimes: watchedTimes,
                remainingTimes: remainingTimes,
                cid: product.cid,
                adVideoId: globalData.adVideoId,
                examine: globalData.examine,
                shareTip: globalData.shareTip || ''
            });
            
            wx.setNavigationBarTitle({
                title: product.name
            });
        } else {
            wx.showToast({
                title: '数据加载失败',
                icon: 'none'
            });
        }
    },

    handleReceive: function() {
        var that = this;
        
        if (that.data.videoTimes === 0) {
            // 无需观看广告，直接领取
            that.ok();
        } else if (that.data.remainingTimes > 0) {
            // 需要观看广告
            that.watchVideo();
        } else {
            // 已完成观看，直接领取
            that.ok();
        }
    },

    watchVideo: function() {
        var that = this;

        if (!rewardedVideoAd) {
            rewardedVideoAd = wx.createRewardedVideoAd({
                adUnitId: that.data.adVideoId
            });
            
            rewardedVideoAd.onError(function(err) {
                console.error("视频广告加载失败:", err);
                wx.showModal({
                    title: "提示",
                    content: "视频广告加载失败",
                    showCancel: false
                });
            });
        }

        rewardedVideoAd.show().catch(function() {
            rewardedVideoAd.load().then(function() {
                rewardedVideoAd.show();
            });
        });

        rewardedVideoAd.onClose(function(res) {
            if (that._unloaded) return;
            if (res && res.isEnded || void 0 === res) {
                // 视频观看完成
                wx.login({
                    success: function(code) {
                        utils.getRequest("/Api/recordVideoWatch.php?code=" + code.code + "&cid=" + that.data.cid).then(function(res) {
                            if (that._unloaded) return;
                            console.log("观看记录返回:", res);
                            if (res.data.code == 200) {
                                var watchedTimes = res.data.data.watched_times || 0;
                                var remainingTimes = Math.max(0, that.data.videoTimes - watchedTimes);
                                
                                that.setData({
                                    watchedTimes: watchedTimes,
                                    remainingTimes: remainingTimes
                                });

                                wx.showModal({
                                    title: "观看进度",
                                    content: "已观看 " + watchedTimes + "/" + that.data.videoTimes + " 次视频广告" +
                                             (res.data.data.is_completed ? "，达到领取条件！" : "，还需观看 " + remainingTimes + " 次"),
                                    showCancel: false,
                                    success: function() {
                                        if (res.data.data.is_completed) {
                                            if (that.data.examine == 3) {
                                                that.setData({
                                                    shareShow: true
                                                });
                                            } else if (that.data.examine == 2 || that.data.videoTimes == 1) {
                                                that.ok();
                                            }
                                        }
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
                            console.error(err);
                            wx.showModal({
                                title: "提示",
                                content: "网络请求超时",
                                showCancel: false
                            });
                        });
                    }
                });
            } else {
                wx.showModal({
                    title: "提示",
                    content: "视频未完整观看，无法获取奖励",
                    showCancel: false
                });
            }

            rewardedVideoAd.offClose();
        });
    },

    ok: function() {
        var that = this;
        that.setData({
            shareShow: false
        });

        wx.login({
            success: function(code) {
                utils.getRequest("/Api/getCarmel.php?code=" + code.code + "&cid=" + that.data.cid).then(function(res) {
                    console.log("领取卡密返回:", res);
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
                                        
                                        // 返回上一页并刷新
                                        returnTimer = setTimeout(function() {
                                            wx.navigateBack({
                                                success: function() {
                                                    var pages = getCurrentPages();
                                                    var prevPage = pages[pages.length - 1];
                                                    if (prevPage && prevPage.getClassDatas) {
                                                        prevPage.getClassDatas();
                                                    }
                                                }
                                            });
                                        }, 1500);
                                    }
                                });
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
                    console.error(err);
                    wx.showModal({
                        title: "提示",
                        content: "网络请求超时",
                        showCancel: false
                    });
                });
            }
        });
    },

    popupShow: function() {
        this.setData({
            shareShow: false
        });
    },

    copyIntroduce: function() {
        var that = this;
        // 移除HTML标签，只复制纯文本
        var plainText = that.data.introduce.replace(/<[^>]+>/g, '').replace(/&nbsp;/g, ' ').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
        
        wx.setClipboardData({
            data: plainText,
            success: function() {
                wx.showToast({
                    title: '复制成功',
                    icon: 'success',
                    duration: 2000
                });
            },
            fail: function() {
                wx.showToast({
                    title: '复制失败',
                    icon: 'none',
                    duration: 2000
                });
            }
        });
    },

    onUnload: function() {
        this._unloaded = true;
        if (returnTimer) {
            clearTimeout(returnTimer);
            returnTimer = null;
        }
        if (rewardedVideoAd) {
            rewardedVideoAd.offError();
            rewardedVideoAd.offClose();
            rewardedVideoAd = null;
        }
    },

    onShareAppMessage: function() {
        var pages = getCurrentPages();
        var prevPage = pages[pages.length - 2];
        var shareData = prevPage ? prevPage.data.data : {};
        
        return {
            title: shareData.shareTitle || "这是个神奇的东西",
            path: "/pages/index/index",
            imageUrl: shareData.shareImg || "../../img/share.jpg"
        };
    }
});
