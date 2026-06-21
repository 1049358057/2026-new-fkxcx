var app = getApp(), utils = require("../../utils/util");
let interstitialAd = null

Page({
    data: {
        tutorialContent: ''
    },

    onLoad: function onLoad() {
        if (wx.createInterstitialAd) {
            interstitialAd = wx.createInterstitialAd({
                adUnitId: 'adunit-edee68f23e961e5e'
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
        
        this.getTutorialContent();
    },

    getTutorialContent: function() {
        var that = this;
        wx.showLoading({
            title: "加载中"
        });
        wx.login({
            success: function success(code) {
                utils.getRequest("/Api/getTutorial.php?code=" + code.code).then(function(res) {
                    console.log(res);
                    wx.hideLoading();
                    if (res.data.code == 200) {
                        that.setData({
                            tutorialContent: res.data.tutorial_content || '暂无教程内容'
                        });
                    } else {
                        wx.showModal({
                            title: "提示",
                            content: res.data.msg,
                            showCancel: !1,
                            confirmText: "重试",
                            success: function success() {
                                that.getTutorialContent();
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
                            that.getTutorialContent();
                        }
                    });
                });
            }
        });
    },

    onShareAppMessage: function onShareAppMessage() {
        return {
            title: "使用教程",
            path: "/pages/tutorial/tutorial",
            imageUrl: "../../img/share.jpg"
        };
    }
}); 