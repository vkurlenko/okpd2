import { Component } from '@angular/core';
import { Tree } from 'primeng/tree';
import { ConfirmationService, MenuItem, Message, MessageService, TreeNode } from 'primeng/api';
import { NodeService } from "../api/services/node.service";
import { FormBuilder, Validators } from '@angular/forms';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})

export class AppComponent {
  display = false;
  displayAddDialog = false;
  items: MenuItem[] = [];
  loading = false;
  node?: TreeNode;
  nodeForm?: any;
  nodes: TreeNode[] = [];
  prevNodes: TreeNode[] = [];
  selectedFile?: TreeNode;
  title = 'my-app';
  uploadedFiles: any[] = [];
  msgs: Message[] = [];
  constructor(private nodeService: NodeService,
              private fb: FormBuilder,
              private confirmationService: ConfirmationService,
              private messageService: MessageService) { }

  ngOnInit(): void {
    this.loadNodes();
    Tree.prototype.isNodeLeaf = (node) => node.leaf;

    this.nodeForm = this.fb.group({
      id: [''],
      global_id: ['', [Validators.required]],
      idx: ['', [Validators.required]],
      kod: ['', [Validators.required]],
      name: ['', [Validators.required]],
      nomdescr: [''],
      razdel: ['', [Validators.required]],
    });

    this.items = [
      {label: 'View', icon: 'pi pi-search', command: (event) => this.viewNode(this.selectedFile)},
      {label: 'Edit', icon: 'pi pi-pencil', command: (event) => this.editNode(this.selectedFile)},
      {label: 'Delete', icon: 'pi pi-times', command: (event) => this.deleteNode(this.selectedFile)}
    ];
  }

  confirm1() {
    this.confirmationService.confirm({
      message: 'Are you sure that you want to proceed?',
      header: 'Confirmation',
      icon: 'pi pi-exclamation-triangle',
      accept: () => {
        this.msgs = [{severity:'info', summary:'Confirmed', detail:'You have accepted'}];
      },
      reject: () => {
        this.msgs = [{severity:'info', summary:'Rejected', detail:'You have rejected'}];
      }
    });
  }

  nodeExpand(event: any) {
    if (event.node) {
      this.loading = true;
      this.nodeService.getLazyFiles(event.node).then(nodes => {
        event.node.children = nodes;
        this.loading = false;
      });
    }
  }

  onSave(): void {
    if (this.nodeForm?.valid) {
      if (this.nodeForm.value.id) {
        this.updateNode();
      } else {
        this.addNode();
      }
    } else {
      Object.keys(this.nodeForm?.controls).forEach((key) => {
        if (this.nodeForm?.get(key)?.invalid) {
          this.nodeForm?.get(key)?.markAsDirty();
        }
      });
    }
  }

  onUpload(event: any): void {
    for(let file of event.files) {
      this.uploadedFiles.push(file);
    }

    const formData = new FormData();
    formData.append("myfile", this.uploadedFiles[0], this.uploadedFiles[0].name);

    this.loading = true;
    this.nodes = [];
    this.nodeService.upload(formData).then(result => {
      console.log(result);
      if (result) {
        this.loadNodes();
      } else {
        this.nodes = this.prevNodes;
      }
    });
  }

  showAddDialog(): void {
    this.displayAddDialog = true;
  }

  private addNode(): void {
    this.nodeService.addNode(this.nodeForm.value).then(result => {
      this.loadNodes();
      this.displayAddDialog = false;
    });
  }

  private deleteNode(node: TreeNode | undefined): void {
    this.confirm1();
    this.confirmationService.confirm({
      message: 'Do you want to delete this code?',
      header: 'Delete Confirmation',
      icon: 'pi pi-info-circle',
      accept: () => {
        this.nodeService.deleteNode(node).then(result => {
          console.log(result);
          this.messageService.add({severity:'info', summary:'Confirmed', detail:'Record deleted'});
          this.loadNodes();
        });
      }
    });
  }

  private editNode(item: TreeNode | undefined): void {
    if (item) {
      this.nodeForm.get('id')?.patchValue(item.data.id);
      this.nodeForm.get('global_id')?.patchValue(item.data.global_id);
      this.nodeForm.get('idx')?.patchValue(item.data.idx);
      this.nodeForm.get('kod')?.patchValue(item.data.kod);
      this.nodeForm.get('name')?.patchValue(item.data.name);
      this.nodeForm.get('nomdescr')?.patchValue(item.data.nomdescr);
      this.nodeForm.get('razdel')?.patchValue(item.data.razdel);
    }
    this.displayAddDialog = true;
  }

  private loadNodes(): void {
    // this.nodeService.getTestNodes().then(nodes => this.nodes = nodes);
    this.nodeService.getNodes().then(nodes => {
      this.nodes = nodes;
      this.prevNodes = nodes;
      this.loading = false;
    });
  }

  private viewNode(item: TreeNode | undefined): void {
    this.display = true;
    this.node = item;
    console.log(item)
  }

  private updateNode(): void {
    this.nodeService.updateNode(this.nodeForm.value).then(result => {
      this.loadNodes();
      this.displayAddDialog = false;
    });
  }
}
