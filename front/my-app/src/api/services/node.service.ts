import { Injectable } from '@angular/core';
import { TreeNode } from "primeng/api";
import { HttpClient } from "@angular/common/http";
import { tap } from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class NodeService {

  constructor(private http: HttpClient) { }

  getTestNodes() {
    return this.http.get<any>('assets/files.json')
      .toPromise()
      .then(res => <TreeNode[]>res.data);
  }

  getNodes() {
    return this.http.get<any>('tree')
      .toPromise()
      .then(res => <TreeNode[]>res.data);
  }

  getLazyFiles(node: any) {
    return this.http.get<any>('children', {
      params : {
        kod: node.data.kod,
        level: node.data.level
      }
    })
      .toPromise()
      .then(res => <TreeNode[]>res.data);
  }

  addNode(form: any) {
    return this.http.post('add', form).pipe(
      tap((response) => {
        console.log(response);
      })
    )
      .toPromise()
      .then();
  }

  updateNode(form: any) {
    return this.http.post('update', form).pipe(
      tap((response) => {
        console.log(response);
      })
    )
      .toPromise()
      .then();
  }

  deleteNode(node: any) {
    return this.http.get<any>('delete', {
      params : {
        id: node.data.id
      }
    })
      .toPromise()
      .then();
  }

  upload(formData: any) {
    return this.http.post('uploader', formData).pipe(
      tap((response) => {
        console.log(response);
      })
    )
      .toPromise()
      .then();
  }
}
