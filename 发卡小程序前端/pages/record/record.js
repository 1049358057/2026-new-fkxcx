var app = getApp(), utils = require("../../utils/util");
let interstitialAd = null
Page({
    data: {
        recordList: [],
        userid: 0,
        examine: 0
    },
    onLoad: function onLoad(options) {
      if (wx.createInterstitialAd) {
        interstitialAd = wx.createInterstitialAd({
          adUnitId: '你的广告ID'
        })
        interstitialAd.onLoad(() => {console.log('插屏广告加载成功')})
        interstitialAd.onError((err) => {
          console.error('插屏广告加载失败', err)
        })
        interstitialAd.onClose(() => {console.log('插屏广告关闭')})
      }
      if (interstitialAd) {
        interstitialAd.show().catch((err) => {
          console.error(err)
        })
      }
 
    },

    getRecord: function getRecord() {
        var that = this;
        wx.showLoading({
            title: "加载中"
        });
        wx.login({
            success: function success(code) {
                utils.getRequest("/Api/getRecord.php?code=" + code.code).then(function(res) {
                    console.log(res);
                    wx.hideLoading();
                    if (res.data.code == 200) {
                        that.setData({
                            recordList: res.data.recordList,
                            userid: res.data.userid,
                            examine: res.data.examine
                        });
                    } else {
                        wx.showModal({
                            title: "提示",
                            content: res.data.msg,
                            showCancel: !1,
                            confirmText: "重试",
                            success: function success() {
                                that.getRecord();
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
                            that.getRecord();
                        }
                    });
                });
            }
        });
    },
    copyUser: function copyUser() {
        var that = this;
        wx.setClipboardData({
            data: that.data.userid,
            success: function success(a) {
                wx.showToast({
                    title: "复制成功",
                    duration: 1200
                });
            }
        });
    },
    copykm: function copykm(t) {
        console.log(t);
        var id = t.currentTarget.dataset.key, km = this.data.recordList[id].km;
        wx.setClipboardData({
            data: km,
            success: function success(a) {
                wx.showToast({
                    title: "复制成功",
                    duration: 1200
                });
            }
        });
    },
    copyUsetip: function copyUsetip(t) {
        console.log(t);
        var id = t.currentTarget.dataset.key, usetip = this.data.recordList[id].usetip;
        wx.setClipboardData({
            data: usetip,
            success: function success(a) {
                wx.showToast({
                    title: "复制成功",
                    duration: 1200
                });
            }
        });
    },
    onShow: function onShow() {
        this.getRecord();
    }
});