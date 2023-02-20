// Package subapi
// @Time:2023/02/20 07:43
// @File:main.go
// @SoftWare:Goland
// @Author:feiyang
// @Contact:TG@feiyangdigital

package main

import (
	"encoding/base64"
	"fmt"
	"github.com/gin-gonic/gin"
	"log"
	"net/http"
)

func bota(url string) string {
	decodeBytes, err := base64.StdEncoding.DecodeString(url)
	if err != nil {
		log.Fatalln(err)
	}
	return string(decodeBytes)
}

func getRedirectUrl(url string) any {
	client := &http.Client{}
	r, _ := http.NewRequest("GET", url, nil)
	r.Header.Add("user-agent", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36")
	resp, err := client.Do(r)
	if err != nil {
		return nil
	}
	defer resp.Body.Close()
	reurl := fmt.Sprintf("%v", resp.Request.URL)
	return reurl
}

func setupRouter() *gin.Engine {
	gin.SetMode(gin.ReleaseMode)
	r := gin.Default()

	r.POST("/go", func(context *gin.Context) {
		url := context.PostForm("shortUrl")
		realurl := bota(url)
		if str, ok := getRedirectUrl(realurl).(string); ok {
			context.JSON(200, gin.H{
				"code": 0,
				"msg":  "success",
				"data": str,
			})
		} else {
			context.JSON(200, gin.H{
				"msg": "有可能是网络问题，请再试一次",
			})
		}
	})
	return r
}

func main() {
	r := setupRouter()
	r.Run(":8090")
}
