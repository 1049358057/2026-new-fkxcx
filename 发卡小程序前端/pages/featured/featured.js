var app = getApp(), utils = require("../../utils/util");
let interstitialAd = null;

Page({
    data: {
        data: {}
    },

    onLoad: function onLoad() {
        if (wx.createInterstitialAd) {
            interstitialAd = wx.createInterstitialAd({
                adUnitId: '你的广告ID'
            });
            interstitialAd.onLoad(() => {console.log('插屏广告加载成功')});
            interstitialAd.onError((err) => {console.error('插屏广告加载失败', err)});
            interstitialAd.onClose(() => {console.log('插屏广告关闭')});
        }
        if (interstitialAd) {
            interstitialAd.show().catch((err) => {
                console.error(err)
            });
        }
        
        this.getFeaturedData();
    },

    getFeaturedData: function() {
        var that = this;
        wx.showLoading({
            title: "加载中"
        });
        wx.login({
            success: function success(code) {
                utils.getRequest("/Api/classDatas.php?code=" + code.code).then(function(res) {
                    console.log(res);
                    wx.hideLoading();
                    if (res.data.code == 200) {
                        that.setData({
                            data: res.data.data
                        });
                    } else {
                        wx.showModal({
                            title: "提示",
                            content: res.data.msg,
                            showCancel: !1,
                            confirmText: "重试",
                            success: function success() {
                                that.getFeaturedData();
                            }
                        });
                    }
                }).catch(function(res) {
                    wx.hideLoading();
                    console.log(res);
                    wx.showModal({
                        title: "提示",
                        content: "网络请求超时",
                        confirmText: "重试",
                        success: function success() {
                            that.getFeaturedData();
                        }
                    });
                });
            }
        });
    },

    navigateToMiniProgram: function(e) {
        const appId = e.currentTarget.dataset.appid;
        const path = e.currentTarget.dataset.path;
        if (appId) {
            wx.navigateToMiniProgram({
                appId: appId,
                path: path,
                success(res) {
                    console.log('跳转成功', res);
                },
                fail(err) {
                    console.error('跳转失败', err);
                }
            });
        }
    },

    onShareAppMessage: function onShareAppMessage() {
        return {
            title: "精品小程序推荐",
            path: "/pages/featured/featured",
            imageUrl: "../../img/share.jpg"
        };
    }
});
